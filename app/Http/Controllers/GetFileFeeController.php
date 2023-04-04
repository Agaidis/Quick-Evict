<?php

namespace App\Http\Controllers;

use App\CivilUnique;
use App\CourtDetails;
use App\ErrorLog;
use App\GeneralAdmin;
use App\GeoLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use JavaScript;
use GMaps;
use Illuminate\Support\Facades\Log;


class GetFileFeeController extends Controller
{
    //
    /**
     * Show the application dashboard.
     * @param $request
     * @return \Illuminate\Http\Response
     */

    public function view() {

        $counties = CourtDetails::distinct()->orderBy('county')->get(['county']);
        $isStep2 = false;

        return view('getFileFee', compact('counties','isStep2'));
    }
    public function index(Request $request )
    {
        if (Auth::guest()) {
            return view('/login');
        } else {
            try {
                $isStep2 = true;
                $selectedCounty = $request->county;
                $counties = CourtDetails::distinct()->orderBy('county')->get(['county']);
                $geoData = GeoLocation::where('county', $request->county)->orderBy('magistrate_id', 'ASC')->get();
                $map = new GMaps;

                JavaScript::put([
                    'geoData' => $geoData,
                    'userId' => Auth::user()->role,
                    'userEmail' => Auth::user()->email
                ]);

                return view('getFileFee', compact('map', 'counties', 'selectedCounty', 'isStep2'));

            } catch (Exception $e) {
                $errorMsg = new ErrorLog();
                $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

                $errorMsg->save();
                return view('dashboard');
            }
        }
    }

    public function getFilingFee( Request $request ) {
        try {
            $courtDetails = CourtDetails::where('magistrate_id', $request->courtNumber)->first();
            $tenantNum = (int)$request->numDefs;
            $additionalTenantAmt = 1;
            $additionalTenantFee = 0;
            $distance = 0;
            $calculatedFee = 0;

            if ($request->fileType == 'ltc') {

                /*                      LANDLORD TENANT COMPLAINT                */

                if ($tenantNum == 2) {
                    $upTo2000 = $courtDetails->two_defendant_up_to_2000;
                    $btn20014000 = $courtDetails->two_defendant_between_2001_4000;
                    $greaterThan4000 = $courtDetails->two_defendant_greater_than_4000;
                } else if ($tenantNum == 1) {
                    $upTo2000 = $courtDetails->one_defendant_up_to_2000;
                    $btn20014000 = $courtDetails->one_defendant_between_2001_4000;
                    $greaterThan4000 = $courtDetails->one_defendant_greater_than_4000;
                } else {
                    $upTo2000 = $courtDetails->three_defendant_up_to_2000;
                    $btn20014000 = $courtDetails->three_defendant_between_2001_4000;
                    $greaterThan4000 = $courtDetails->three_defendant_greater_than_4000;
                    if ($courtDetails->additional_tenant != '' && $courtDetails->additional_tenant != 0 ) {
                        $additionalTenantAmt = $courtDetails->additional_tenant;
                    }
                }

                if ($tenantNum > 3) {
                    $multiplyBy = $tenantNum - 3;
                    $additionalTenantFee = (float)$additionalTenantAmt * $multiplyBy;
                }

                $noCommaTotalFees = str_replace([',', '$',' '],['', '', ''], $request->totalJudgment);

                if ($noCommaTotalFees < 2000) {
                    $filingFee = $upTo2000 + $additionalTenantFee;
                } else if ($noCommaTotalFees >= 2000 && $noCommaTotalFees <= 4000) {
                    $filingFee = $btn20014000 + $additionalTenantFee;
                } else if ($noCommaTotalFees > 4000) {
                    $filingFee = $greaterThan4000 + $additionalTenantFee;
                } else {
                    $filingFee = 'Didnt Work';
                }

                $errorMsg = new ErrorLog();
                $errorMsg->payload = 'Court Details: ' . serialize($courtDetails);
                $errorMsg->save();

                if ($courtDetails->is_distance_fee === 1) {

                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = 'atleast im here';
                    $errorMsg->save();

                    $newFile = new NewFileController();
                    $geoData = GeoLocation::where('magistrate_id', $request->courtNumber)->first();

                    $courtAddress = $geoData->address_line_one . ' ' . $geoData->address_line_two;

                    $distance = $newFile->getDistance( $courtAddress, $request->userAddress, $request->fileType );

                    $mileFee = GeneralAdmin::where('name', 'mile_fee')->value('value');

                    $calculatedFee = $distance * $mileFee;

                    $calculatedFee = number_format($calculatedFee, 2);
                    $filingFee = $filingFee + $calculatedFee;

                }

                $filingFee = number_format($filingFee, 2);

                $returnArray = array('filingFee' => $filingFee, 'distance' => $distance, 'calculatedFee' => $calculatedFee);

                return $returnArray;
            } else if ($request->fileType === 'oop') {

                /*                      ORDER OF POSSESSION                 */

                $errorMsg = new ErrorLog();
                $errorMsg->payload = 'im in oop';
                $errorMsg->save();

                if ($tenantNum == 2) {
                    $oop = $courtDetails->two_defendant_out_of_pocket;
                } else if ($tenantNum == 1) {
                    $oop = $courtDetails->one_defendant_out_of_pocket;
                } else {
                    $oop = $courtDetails->three_defendant_out_of_pocket;
                    if ($courtDetails->additional_tenant != '' && $courtDetails->additional_tenant != 0 ) {
                        $additionalTenantAmt = $courtDetails->additional_tenant;
                    }
                }

                if ($tenantNum > 3) {
                    $multiplyBy = $tenantNum - 3;
                    $additionalTenantFee = (float)$additionalTenantAmt * $multiplyBy;
                }

                $totalFees = (float)$request->totalJudgment;

                $noCommaTotalFees = str_replace(['$',',',' '],['','',''], $totalFees);

                if ($noCommaTotalFees < 2000) {
                    $filingFee = $oop + $additionalTenantFee;
                } else if ($noCommaTotalFees >= 2000 && $noCommaTotalFees <= 4000) {
                    $filingFee = $oop + $additionalTenantFee;
                } else if ($noCommaTotalFees > 4000) {
                    $filingFee = $oop + $additionalTenantFee;
                } else {
                    $filingFee = 'Didnt Work';
                }

                $errorMsg = new ErrorLog();
                $errorMsg->payload = 'Filing Fee 178: ' . $filingFee;
                $errorMsg->save();

                if ($courtDetails->oop_distance_fee === 1) {

                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = 'in oop distance fee';
                    $errorMsg->save();

                    $newFile = new NewFileController();
                    $geoData = GeoLocation::where('magistrate_id', $request->courtNumber)->first();



                    $courtAddress = $geoData->address_line_one . ' ' . $geoData->address_line_two;

                    $distance = $newFile->getDistance( $courtAddress, $request->userAddress, $request->fileType );

                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = 'Distance: ' . $distance;
                    $errorMsg->save();

                    $mileFee = GeneralAdmin::where('name', 'mile_fee')->value('value');

                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = 'mile fee: ' . $mileFee;
                    $errorMsg->save();

                    $calculatedFee = $distance * $mileFee;

                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = 'Calculated Fee: ' . $calculatedFee;
                    $errorMsg->save();

                    $calculatedFee = number_format($calculatedFee, 2);

                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = 'Calculated Fee 2: ' . $calculatedFee;
                    $errorMsg->save();

                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = 'filing Fee 1: ' . $filingFee;
                    $errorMsg->save();

                    $filingFee = $filingFee + $calculatedFee;

                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = 'filing Fee 2: ' . $filingFee;
                    $errorMsg->save();
                }

                $filingFee = number_format($filingFee, 2);

                $returnArray = array('filingFee' => $filingFee, 'distance' => $distance, 'calculatedFee' => $calculatedFee);

                return $returnArray;
            } else if ($request->fileType === 'civil') {

                /*                  CIVIL COMPLAINT             */

                $civilDetails = CivilUnique::where('court_details_id', $courtDetails->id)->first();
                $totalJudgment = str_replace([' ', ',', '$'],['', '', ''], $request->totalJudgment);

                if ($tenantNum > 1) {
                    if ($request->deliveryType == 'mail') {
                        if ($totalJudgment <= 500) {
                            $filingFee = $civilDetails->under_500_2_def_mail;
                        } else if ($totalJudgment > 500 && $totalJudgment <= 2000) {
                            $filingFee = $civilDetails->btn_500_2000_2_def_mail;
                        } else if ($totalJudgment > 2000 && $totalJudgment < 4001) {
                            $filingFee = $civilDetails->btn_2000_4000_2_def_mail;
                        } else if ($totalJudgment > 4000 && $totalJudgment < 12001) {
                            $filingFee = $civilDetails->btn_4000_12000_2_def_mail;
                        }
                    } else if ($request->deliveryType == 'constable') {
                        if ($totalJudgment <= 500) {
                            $filingFee = $civilDetails->under_500_2_def_constable;
                        } else if ($totalJudgment > 500 && $totalJudgment <= 2000) {
                            $filingFee = $civilDetails->btn_500_2000_2_def_constable;
                        } else if ($totalJudgment > 2000 && $totalJudgment < 4001) {
                            $filingFee = $civilDetails->btn_2000_4000_2_def_constable;
                        } else if ($totalJudgment > 4000 && $totalJudgment < 12001) {
                            $filingFee = $civilDetails->btn_4000_12000_2_def_constable;
                        }
                    }
                } else {
                    if ($request->deliveryType == 'mail') {
                        if ($totalJudgment <= 500) {
                            $filingFee = $civilDetails->under_500_1_def_constable;
                        } else if ($totalJudgment > 500 && $totalJudgment <= 2000) {
                            $filingFee = $civilDetails->btn_500_2000_1_def_constable;
                        } else if ($totalJudgment > 2000 && $totalJudgment < 4001) {
                            $filingFee = $civilDetails->btn_2000_4000_1_def_constable;
                        } else if ($totalJudgment > 4000 && $totalJudgment < 12001) {
                            $filingFee = $civilDetails->btn_4000_12000_1_def_constable;
                        }
                    } else if ($request->deliveryType == 'constable') {
                        if ($totalJudgment <= 500) {
                            $filingFee = $civilDetails->under_500_1_def_constable;
                        } else if ($totalJudgment > 500 && $totalJudgment <= 2000) {
                            $filingFee = $civilDetails->btn_500_2000_1_def_constable;
                        } else if ($totalJudgment > 2000 && $totalJudgment < 4001) {
                            $filingFee = $civilDetails->btn_2000_4000_1_def_constable;
                        } else if ($totalJudgment > 4000 && $totalJudgment < 12001) {
                            $filingFee = $civilDetails->btn_4000_12000_1_def_constable;
                        }
                    }
                }

                $distance = 0;
                $calculatedFee = 0;

                if ($courtDetails->civil_distance_fee === 1) {
                    $newFile = new NewFileController();
                    $geoData = GeoLocation::where('magistrate_id', $request->courtNumber)->first();

                    $courtAddress = $geoData->address_line_one . ' ' . $geoData->address_line_two;

                    $distance = $newFile->getDistance( $courtAddress, $request->userAddress, $request->fileType );

                    $mileFee = GeneralAdmin::where('name', 'mile_fee')->value('value');

                    $calculatedFee = $distance * $mileFee;
                    $calculatedFee = number_format($calculatedFee, 2);
                    $filingFee = $filingFee + $calculatedFee;
                }

                $filingFee = number_format($filingFee, 2);

                $returnArray = array('filingFee' => $filingFee, 'distance' => $distance, 'calculatedFee' => $calculatedFee);

                return $returnArray;

            } else if ($request->fileType === 'none') {
                return 'Failed to Choose File Type';
            }

            return 'success';
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }


    }
}
