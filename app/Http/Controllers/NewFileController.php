<?php

namespace App\Http\Controllers;

use App\GeoLocation;
use Illuminate\Http\Request;
use App\CourtDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use JavaScript;
use GMaps;

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
            $counties = CourtDetails::distinct()->get(['county']);

            return view('newFile', compact('counties'));
        }
    }

    public function proceedToFileTypeWithSelectedCounty(Request $request) {
        if (Auth::guest()) {
            return view('/login');
        } else {
            try {
                $geoData = GeoLocation::where('county', $request->county)->orderBy('magistrate_id', 'ASC')->get();
                $map = new GMaps;
                $fileType = $request->fileType;

                foreach ($geoData as $geo) {
                    $township = CourtDetails::where('magistrate_id', $geo['magistrate_id'])->value('township');
                    $isOnlineAccepted = CourtDetails::where('magistrate_id', $geo['magistrate_id'])->value('online_submission');
                    $geo['township'] = $township;
                    $geo['isOnlineAccepted'] = $isOnlineAccepted;
                }

                JavaScript::put([
                    'geoData' => $geoData,
                    'userId' => Auth::user()->role,
                    'userEmail' => Auth::user()->email
                ]);

                if ($request->fileType == 'ltc') {
                    return view('eviction', compact('map', 'fileType'));
                } else if ($request->fileType == 'oop') {
                    return view('orderOfPossession', compact('map', 'fileType'));
                } else if ($request->fileType == 'civil') {
                    return view('civilComplaint', compact('map', 'fileType'));
                } else {
                    return view('dashboard');
                }
            } catch (Exception $e) {
                $errorDetails = 'NewFileController - error in proceedToFileTypeWithSelectedCounty() method when attempting to navigate user';
                $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
                $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();
                $errorDetails .= PHP_EOL . 'Message ' . $e->getMessage();
                Log::error($errorDetails . PHP_EOL . 'Error Message: ' . $e->getMessage() . PHP_EOL . 'Trace: ' . $e->getTraceAsString());
                mail('andrew.gaidis@gmail.com', 'Proceeding to File Type', $errorDetails);
                return 'failure';
            }
        }
    }
    public function getFilingFee() {

        try {
            $courtNumber = explode('_', $_GET['court_number']);
            $fileType = $_GET['fileType'];
            $filingFee = '';
            $additionalTenantFee = 0;
            $additionalTenantAmt = 1;
            $removeValues = ['$', ','];
            $tenantNum = (int)$_GET['tenant_num'];

            if ($fileType == 'ltc') {

                $additionalRentAmt = str_replace($removeValues, '', $_GET['additional_rent_amt']);
                $attorneyFees = str_replace($removeValues, '', $_GET['attorney_fees']);
                $dueRent = str_replace($removeValues, '', $_GET['due_rent']);
                $unjustDamages = str_replace($removeValues, '', $_GET['unjust_damages']);
                $damageAmt = str_replace($removeValues, '', $_GET['damage_amt']);

                $courtDetails = CourtDetails::where('magistrate_id', $courtNumber[1])->first();

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




            } else if ($fileType === 'oop') {
                if ($tenantNum == 1) {
                    $filingFee = CourtDetails::where('magistrate_id', $courtNumber[1])->value('one_defendant_out_of_pocket');
                } else if ($tenantNum == 2) {
                    $filingFee = CourtDetails::where('magistrate_id', $courtNumber[1])->value('two_defendant_out_of_pocket');
                } else if ((int)$tenantNum >= 3 ) {
                    $filingFee = CourtDetails::where('magistrate_id', $courtNumber[1])->value('three_defendant_out_of_pocket');
                }
            } else if ($fileType === 'civil') {
                if ($tenantNum == 1) {
                    $filingFee = 0;
                } else if ($tenantNum == 2) {
                    $filingFee = 0;
                } else if ((int)$tenantNum >= 3 ) {
                    $filingFee = 0;
                }
            }
            return  $filingFee = number_format((float)$filingFee, 2, '.', '');




        } catch ( Exception $e ) {
            $errorDetails = 'NewFileController - error in getFilingFee() method when attempting to get filing fee';
            $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
            $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();
            $errorDetails .= PHP_EOL . 'Message ' . $e->getMessage();
            Log::error($errorDetails . PHP_EOL . 'Error Message: ' . $e->getMessage() . PHP_EOL . 'Trace: ' . $e->getTraceAsString());
            mail('andrew.gaidis@gmail.com', 'Get Filing Fee', $errorDetails);
            return 'failure';
        }
    }
}
