<?php

namespace App\Http\Controllers;

use App\CivilUnique;
use App\ErrorLog;
use App\GeneralAdmin;
use App\GeoLocation;
use Illuminate\Http\Request;
use App\CourtDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use JavaScript;
use GMaps;
use GuzzleHttp\Client;


class NewFileController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::guest()) {
            return view('/login');
        } else {
            $counties = CourtDetails::distinct()->orderBy('county')->get(['county']);

            return view('home', compact('counties'));
        }
    }

    public function proceedToFileTypeWithSelectedCounty(Request $request) {

            try {



                $geoData = GeoLocation::where('county', $request->county)->orderBy('magistrate_id', 'ASC')->get();
                $map = new GMaps;
                $fileType = $request->fileType;
                $payType = Auth::user()->pay_type;


                foreach ($geoData as $geo) {
                    $township = CourtDetails::where('magistrate_id', $geo['magistrate_id'])->value('township');
                    $isOnlineAccepted = CourtDetails::where('magistrate_id', $geo['magistrate_id'])->value('online_submission');
                    $geo['township'] = $township;
                    $geo['isOnlineAccepted'] = $isOnlineAccepted;
                }

                JavaScript::put([
                    'geoData' => $geoData,
                    'userId' => Auth::user()->role,
                    'userEmail' => Auth::user()->email,
                    'payType' => Auth::user()->pay_type
                ]);
                $userEmail = Auth::user()->email;

                $errorMsg = new ErrorLog();
                $errorMsg->payload = 'file type: ' . $request->fileType . ' User: ' . $userEmail;
                $errorMsg->save();

                if ($request->fileType == 'ltc') {
                    $isComplaintFee = 'no';
                    return view('eviction', compact('map', 'fileType', 'userEmail', 'payType', 'isComplaintFee'));
                } else if ($request->fileType == 'ltcA') {
                    $isComplaintFee = 'yes';
                    return view('eviction', compact('map', 'fileType', 'userEmail', 'payType', 'isComplaintFee'));
                } else if ($request->fileType == 'oop') {
                    $isComplaintFee = 'no';
                    return view('orderOfPossession', compact('map', 'fileType', 'userEmail', 'payType', 'isComplaintFee'));
                } else if ($request->fileType == 'oopA') {
                    $isComplaintFee = 'yes';
                    return view('orderOfPossession', compact('map', 'fileType', 'userEmail', 'payType', 'isComplaintFee'));
                } else if ($request->fileType == 'civil') {
                    return view('civilComplaint', compact('map', 'fileType', 'userEmail', 'payType' ));
                } else {
                    return view('dashboard');
                }
            } catch (Exception $e) {
                $errorMsg = new ErrorLog();
                $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

                $errorMsg->save();
                return 'failure 11';
            }
    }
    public function getFilingFee() {

        try {
            $courtNumber = explode('_', $_GET['court_number']);
            $fileType = $_GET['fileType'];
            $filingFee = '';
            $additionalTenantFee = 0;
            $additionalTenantAmt = 1;
            $removeValues = [' ', '$', ','];
            $tenantNum = (int)$_GET['tenant_num'];
            $courtDetails = CourtDetails::where('magistrate_id', $courtNumber[1])->first();

            if ($fileType == 'ltc' || $fileType == 'ltcA') {

                if ($courtDetails->is_distance_fee === 1) {
                    $geoData = GeoLocation::where('magistrate_id', $courtNumber[1])->first();

                    $courtAddress = $geoData->address_line_one . ' ' . $geoData->address_line_two;

                    $userAddress = $_GET['userAddress'];

                    $distance = $this->getDistance( $courtAddress, $userAddress, $_GET['fileType'] );

                    $mileFee = GeneralAdmin::where('name', 'mile_fee')->value('value');

                    $calculatedFee = $distance * $mileFee;
                    $calculatedFee = number_format($calculatedFee, 2);
                } else {
                    $courtAddress = '';
                    $mileFee = '';
                    $calculatedFee = '';
                }

                $additionalRentAmt = str_replace($removeValues, '', $_GET['additional_rent_amt']);
                $attorneyFees = str_replace($removeValues, '', $_GET['attorney_fees']);
                $dueRent = str_replace($removeValues, '', $_GET['due_rent']);
                $unjustDamages = str_replace($removeValues, '', $_GET['unjust_damages']);
                $damageAmt = str_replace($removeValues, '', $_GET['damage_amt']);

                if ($tenantNum == 1) {
                    $upTo2000 = $courtDetails->one_defendant_up_to_2000;
                    $btn20014000 = $courtDetails->one_defendant_between_2001_4000;
                    $greaterThan4000 = $courtDetails->one_defendant_greater_than_4000;
                } else if ($tenantNum == 2) {
                    $upTo2000 = $courtDetails->two_defendant_up_to_2000;
                    $btn20014000 = $courtDetails->two_defendant_between_2001_4000;
                    $greaterThan4000 = $courtDetails->two_defendant_greater_than_4000;
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

                if (is_numeric($_GET['additional_rent_amt'])) {
                    $totalFees = (float)$additionalRentAmt + (float)$attorneyFees + (float)$dueRent + (float)$unjustDamages + (float)$damageAmt;
                } else {
                    $totalFees = (float)$attorneyFees + (float)$dueRent + (float)$unjustDamages + (float)$damageAmt;
                }

                $noCommaTotalFees = str_replace(',','', $totalFees);

                if ($noCommaTotalFees < 2000) {
                    $filingFee = $upTo2000 + $additionalTenantFee;
                } else if ($noCommaTotalFees >= 2000 && $noCommaTotalFees <= 4000) {
                    $filingFee = $btn20014000 + $additionalTenantFee;
                } else if ($noCommaTotalFees > 4000) {
                    $filingFee = $greaterThan4000 + $additionalTenantFee;
                }




            } else if ($fileType === 'oop' || $fileType === 'oopA') {

                if ($courtDetails->oop_distance_fee === 1) {

                    $geoData = GeoLocation::where('magistrate_id', $courtNumber[1])->first();

                    $courtAddress = $geoData->address_line_one . ' ' . $geoData->address_line_two;

                    $userAddress = $_GET['userAddress'];

                    $distance = $this->getDistance( $courtAddress, $userAddress, $_GET['fileType'] );

                    $mileFee = GeneralAdmin::where('name', 'mile_fee')->value('value');

                    $calculatedFee = $distance * $mileFee;

                    $calculatedFee = number_format($calculatedFee, 2);


                } else {
                    $courtAddress = '';
                    $mileFee = '';
                    $calculatedFee = '';
                }

                if ($tenantNum == 1) {
                    $filingFee = CourtDetails::where('magistrate_id', $courtNumber[1])->value('one_defendant_out_of_pocket');
                } else if ($tenantNum == 2) {
                    $filingFee = CourtDetails::where('magistrate_id', $courtNumber[1])->value('two_defendant_out_of_pocket');
                } else if ((int)$tenantNum >= 3 ) {
                    $filingFee = CourtDetails::where('magistrate_id', $courtNumber[1])->value('three_defendant_out_of_pocket');
                }
            } else if ($fileType === 'civil') {

                if ($courtDetails->civil_distance_fee === 1) {
                    $geoData = GeoLocation::where('magistrate_id', $courtNumber[1])->first();

                    $courtAddress = $geoData->address_line_one . ' ' . $geoData->address_line_two;

                    $userAddress = $_GET['userAddress'];

                    $distance = $this->getDistance( $courtAddress, $userAddress, $_GET['fileType'] );

                    $mileFee = GeneralAdmin::where('name', 'mile_fee')->value('value');

                    $calculatedFee = $distance * $mileFee;
                    $calculatedFee = number_format($calculatedFee, 2);
                } else {
                    $courtAddress = '';
                    $mileFee = '';
                    $calculatedFee = '';
                }


                $totalJudgment = str_replace($removeValues,['', '', ''], $_GET['total_judgment']);
                $civilDetails = CivilUnique::where('court_details_id', $courtDetails->id)->first();

                if ($tenantNum > 1) {
                    if ($_GET['delivery_type'] == 'mail') {
                        if ($totalJudgment <= 500) {
                            $filingFee = $civilDetails->under_500_2_def_mail;
                        } else if ($totalJudgment > 500 && $totalJudgment <= 2000) {
                            $filingFee = $civilDetails->btn_500_2000_2_def_mail;
                        } else if ($totalJudgment > 2000 && $totalJudgment < 4001) {
                            $filingFee = $civilDetails->btn_2000_4000_2_def_mail;
                        } else if ($totalJudgment > 4000 && $totalJudgment < 12001) {
                            $filingFee = $civilDetails->btn_4000_12000_2_def_mail;
                        }
                    } else if ($_GET['delivery_type'] == 'constable') {
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
                    if ($_GET['delivery_type'] == 'mail') {
                        if ($totalJudgment <= 500) {
                            $filingFee = $civilDetails->under_500_1_def_constable;
                        } else if ($totalJudgment > 500 && $totalJudgment <= 2000) {
                            $filingFee = $civilDetails->btn_500_2000_1_def_constable;
                        } else if ($totalJudgment > 2000 && $totalJudgment < 4001) {
                            $filingFee = $civilDetails->btn_2000_4000_1_def_constable;
                        } else if ($totalJudgment > 4000 && $totalJudgment < 12001) {
                            $filingFee = $civilDetails->btn_4000_12000_1_def_constable;
                        }
                    } else if ($_GET['delivery_type'] == 'constable') {
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
            }

            $response = array(
                'filingFee' => number_format((float)$filingFee, 2, '.', ''),
                'courtAddress' => $courtAddress,
                'mileFee' => $mileFee,
                'calculatedFee' => $calculatedFee
            );

            return $response;

        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            $errorDetails = 'NewFileController - error in getFilingFee() method when attempting to get filing fee';
            $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
            $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();
            $errorDetails .= PHP_EOL . 'Message ' . $e->getMessage();
            Log::error($errorDetails . PHP_EOL . 'Error Message: ' . $e->getMessage() . PHP_EOL . 'Trace: ' . $e->getTraceAsString());
            mail('andrew.gaidis@gmail.com', 'Get Filing Fee', $errorDetails);
            return 'failure 22';
        }
    }

    public function getLatLngOfAddress($address) {

        try {
            // url encode the address
            $address = urlencode($address);

            // google map geocode api url
            $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyAfPLSbGAHZkEd-8DDB0FcGSlhrV9LQMGM";

            // get the json response
            $resp_json = file_get_contents($url);

            // decode the json
            $resp = json_decode($resp_json, true);

            Log::info($resp);

            // response status will be 'OK', if able to geocode given address
            if($resp['status']=='OK'){

                // get the important data
                $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
                $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
                $formatted_address = isset($resp['results'][0]['formatted_address']) ? $resp['results'][0]['formatted_address'] : "";

                // verify if data is complete
                if($lati && $longi && $formatted_address){

                    // put the data in the array
                    $data_arr = array();

                    array_push(
                        $data_arr,
                        $lati,
                        $longi,
                        $formatted_address
                    );

                    return $data_arr;

                } else{
                    return false;
                }

            }

            else{
                echo "<strong>ERROR: {$resp['status']}</strong>";
                return false;
            }
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }

    }

    public function getDistance($courtAddress, $userAddress, $fileType) {

        try {
            // url encode the address
            $courtAddress = urlencode($courtAddress);
            $userAddress = urlencode($userAddress);

            // google map geocode api url
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=".$courtAddress."&destinations=".$userAddress."&key=AIzaSyAfPLSbGAHZkEd-8DDB0FcGSlhrV9LQMGM";

            // get the json response
            $resp_json = file_get_contents($url);

            // decode the json
            $resp = json_decode($resp_json, true);

            $mileage = $resp['rows'][0]['elements'][0]['distance']['text'];
            $mileage = str_replace(' mi', '', $mileage);

            if ($fileType === 'civil') {
                $mileage = number_format($mileage, 2) * 2;
            } else if ($fileType === 'oop' || $fileType === 'oopA') {
                $mileage = number_format($mileage, 2) * 4;
            } else if ($fileType === 'ltc' || $fileType === 'ltcA') {
                $mileage = number_format($mileage, 2) * 2;
            }

            return $mileage;

        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }
}
