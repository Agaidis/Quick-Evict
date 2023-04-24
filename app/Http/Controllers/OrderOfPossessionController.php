<?php

namespace App\Http\Controllers;

use App\Classes\Mailer;
use App\PDF;
use Illuminate\Support\Facades\Auth;
use App\GeoLocation;
use GMaps;
use App\CourtDetails;
use App\Evictions;
use App\Signature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Stripe\Stripe;
use stdClass;
use Dompdf\Options;
use Dompdf\Dompdf;
use App\ErrorLog;
use Illuminate\Support\Facades\Session;

class OrderOfPossessionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showSamplePDF() {
        $mailer = new Mailer();

        try {
            $magistrateId = str_replace('magistrate_' , '', $_POST['court_number']);
            $courtDetails = CourtDetails::where('magistrate_id', $magistrateId)->first();
            $geoDetails = GeoLocation::where('magistrate_id', $magistrateId)->first();
            $pdfHtml = PDF::where('name', 'oop')->value('html');
            $pdfEditor = new PDFEditController();
            $evictionData = new stdClass();

            if ($_POST['rented_by_val'] == 'rentedByOwner') {
                $plaintiffName = $_POST['owner_name'];
                $btmPlaintiffName = $_POST['owner_name'];
                $plaintiffPhone = $_POST['owner_phone'];
                $plaintiffAddress1 = $_POST['owner_address_1'];
                $plaintiffAddress2 = $_POST['owner_address_2'];
            } else {
                $plaintiffName = $_POST['other_name'] . ' on behalf of ' . $_POST['owner_name'];
                $plaintiffPhone = $_POST['pm_phone'];
                $plaintiffAddress1 = $_POST['pm_address_1'];
                $plaintiffAddress2 = $_POST['pm_address_2'];
                $btmPlaintiffName = $_POST['pm_name'] . ',<br>' . $_POST['other_name'] . ',<br>' . 'On behalf of ' . $_POST['owner_name'] . '<br>' . $plaintiffPhone;
            }

            $tenantName = implode(', ', $_POST['tenant_name']);

            $plaintiffAddress = $plaintiffName .'<br>'. $plaintiffAddress1 .'<br>'. $plaintiffAddress2 .'<br>'. $plaintiffPhone;
            $defendantAddress = $tenantName . '<br>' . $_POST['houseNum'] . ' ' . $_POST['streetName'] . ', ' . $_POST['unit_number'] .'<br> '. $_POST['town'] .', '. $_POST['state'] .' '. $_POST['zipcode'];
            $defendantAddress2 = $_POST['houseNum'] . ' ' . $_POST['streetName'] .' '. $_POST['unit_number'] . '<br><br><span style="position:absolute; margin-top:-10px;">'. $_POST['town'] .', ' . $_POST['state'] .' '. $_POST['zipcode'];
            $docketNumber2 = $_POST['docket_number_2'];

            while (strlen($docketNumber2) < 7) {
                $docketNumber2 = '0' . $docketNumber2;
            }

            $additionalTenantAmt = 1;
            $additionalTenantFee = 0;

            $tenantNum = (int)$_POST['tenant_num'];

            if ($tenantNum == 2) {
                $oop = $courtDetails->two_defendant_out_of_pocket;
            } else if ($_POST['tenant_num'] == 1) {
                $oop = $courtDetails->one_defendant_out_of_pocket;
            } else {
                $oop = $courtDetails->three_defendant_out_of_pocket;
                if ($courtDetails->oop_additional_tenant_fee != '' && $courtDetails->oop_additional_tenant_fee != 0 ) {
                    $additionalTenantAmt = $courtDetails->oop_additional_tenant_fee;
                }
            }

            if ($tenantNum > 3) {
                $multiplyBy = $tenantNum - 3;
                $additionalTenantFee = (float)$additionalTenantAmt * $multiplyBy;
            }

            $totalFees = (float)$_POST['judgment_amount'] + (float)$_POST['costs_original_lt_proceeding'] + $oop + (float)$_POST['attorney_fees'];

            $noCommaTotalFees = str_replace(',','', $totalFees);

            $totalFees = number_format($totalFees, 2);

            if ($noCommaTotalFees < 2000) {
                $filingFee = $oop + $additionalTenantFee;
            } else if ($noCommaTotalFees >= 2000 && $noCommaTotalFees <= 4000) {
                $filingFee = $oop + $additionalTenantFee;
            } else if ($noCommaTotalFees > 4000) {
                $filingFee = $oop + $additionalTenantFee;
            } else {
                $filingFee = 'Didnt Work';
            }

            $filingFee = number_format($filingFee, 2);

            $evictionData->id = '-1';
            $evictionData->plantiff_name = $btmPlaintiffName;
            $evictionData->court_address_line_1 = $geoDetails->address_line_one;
            $evictionData->court_address_line_2 = $geoDetails->address_line_two;
            $evictionData->total_judgement = $totalFees;
            $evictionData->filing_fee = number_format($filingFee, 2);
            $evictionData->docket_number = 'MJ-' . $_POST['docket_number_1'] . '-LT-' . $docketNumber2 . '-' . $_POST['docket_number_3'];
            $evictionData->attorney_fees = $_POST['attorney_fees'];
            $evictionData->judgment_amount = $_POST['judgment_amount'];
            $evictionData->cost_this_proceeding = $oop;
            $evictionData->costs_original_lt_proceeding = $_POST['costs_original_lt_proceeding'];

            $pdfHtml = $pdfEditor->globalHtmlAttributes($pdfHtml, $courtDetails, $plaintiffAddress, $defendantAddress, $_POST['signature_source'], $evictionData);
            $pdfHtml = $pdfEditor->localOOPAttributes($pdfHtml, $evictionData, $defendantAddress2, $btmPlaintiffName);
            $pdfHtml = $pdfEditor->addSampleWatermark($pdfHtml, true);
            $domPdf = new Dompdf();
            $options = new Options();

            $options->setIsRemoteEnabled(true);
            $domPdf->setOptions($options);
            $domPdf->loadHtml($pdfHtml);

            // (Optional) Setup the paper size and orientation
            $domPdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $domPdf->render();

            // Output the generated PDF to Browser
            //$domPdf->stream();
            $domPdf->stream("sample_oop.pdf", array("Attachment" => 0));

            exit(0);

        } catch ( Exception $e) {

            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();

            $mailer->sendMail('andrew.gaidis@gmail.com', 'OOP Preview Error', $e->getMessage(),  $e->getMessage() );
        }
    }

    public function formulatePDF()
    {
        $mailer = new Mailer();

        try {
            $magistrateId = str_replace('magistrate_' , '', $_POST['court_number']);
            $courtDetails = CourtDetails::where('magistrate_id', $magistrateId)->first();
            $geoDetails = GeoLocation::where('magistrate_id', $magistrateId)->first();
            $isOnline = 0;

            if ($courtDetails->online_submission == 'of') {
                $isOnline = 1;
                $status = 'OOP Submitted, $$ needs del';
            } else if ($courtDetails->online_submission === 'otm' ) {
                $status = 'OOP, to be mailed';
            } else if ($courtDetails->online_submission === 'otp') {
                $status = 'OOP Submitted, $$ & file needs DEL';
            } else {
                $status = '';
            }



            $courtNumber = $courtDetails->court_number;

            $isAmtGreaterThanZero = false;

            $courtAddressLine1 = $geoDetails->address_line_one;
            $courtAddressLine2 = $geoDetails->address_line_two;

            $tenantName = implode(', ', $_POST['tenant_name']);

            $additionalTenantAmt = 1;
            $additionalTenantFee = 0;

            $tenantNum = (int)$_POST['tenant_num'];

            if ($tenantNum == 2) {
                $oop = $courtDetails->two_defendant_out_of_pocket;
            } else if ($_POST['tenant_num'] == 1) {
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

            $pmName = $_POST['pm_name'];
            $ownerName = $_POST['owner_name'];

            if ($_POST['rented_by_val'] == 'rentedByOwner') {
                $verifyName = $_POST['owner_name'];
                $plantiffName = $_POST['owner_name'];
                $plantiffPhone = $_POST['owner_phone'];
                $plantiffAddress1 = $_POST['owner_address_1'];
                $plantiffAddress2 = $_POST['owner_address_2'];
            } else {
                $verifyName = $pmName;
                $plantiffName = $_POST['other_name'] . ' on behalf of ' . $_POST['owner_name'];
                $plantiffPhone = $_POST['pm_phone'];
                $plantiffAddress1 = $_POST['pm_address_1'];
                $plantiffAddress2 = $_POST['pm_address_2'];
            }

            $defendantState = $_POST['state'];
            $defendantZipcode = $_POST['zipcode'];
            $defendanthouseNum = $_POST['houseNum'];
            $defendantStreetName = $_POST['streetName'];
            $defendantTown = $_POST['town'];

            $totalFees = (float)$_POST['judgment_amount'] + (float)$_POST['costs_original_lt_proceeding'] + $oop + (float)$_POST['attorney_fees'];

            $noCommaTotalFees = str_replace(',','', $totalFees);

            $totalFees = number_format($totalFees, 2);

            if ($noCommaTotalFees < 2000) {
                $filingFee = $oop + $additionalTenantFee;
            } else if ($noCommaTotalFees >= 2000 && $noCommaTotalFees <= 4000) {
                $filingFee = $oop + $additionalTenantFee;
            } else if ($noCommaTotalFees > 4000) {
                $filingFee = $oop + $additionalTenantFee;
            } else {
                $filingFee = 'Didnt Work';
            }

            if ($noCommaTotalFees > 0) {
                $isAmtGreaterThanZero = true;
            }

            $docketNumber2 = $_POST['docket_number_2'];

            while (strlen($docketNumber2) < 7) {
                $docketNumber2 = '0' . $docketNumber2;
            }

            if (isset($_POST['distance_fee'])) {
                $filingFee = $filingFee + (float)$_POST['distance_fee'];
            }

            $filingFee = number_format($filingFee, 2);



            try {
                $eviction = new Evictions();
                $eviction->status = $status;
                $eviction->total_judgement = $totalFees;
                $eviction->judgment_amount = $_POST['judgment_amount'];
                $eviction->costs_original_lt_proceeding = $_POST['costs_original_lt_proceeding'];
                $eviction->cost_this_proceeding = $oop;
                $eviction->attorney_fees = $_POST['attorney_fees'];
                $eviction->property_address = $defendanthouseNum.' '.$defendantStreetName.'-1'.$defendantTown .',' . $defendantState.' '.$defendantZipcode;
                $eviction->defendant_state = $defendantState;
                $eviction->defendant_zipcode = $defendantZipcode;
                $eviction->defendant_house_num = $defendanthouseNum;
                $eviction->defendant_street_name = $defendantStreetName;
                $eviction->defendant_town = $defendantTown;
                $eviction->tenant_name = $tenantName;
                $eviction->court_number = $courtNumber;
                $eviction->court_address_line_1 = $courtAddressLine1;
                $eviction->court_address_line_2 = $courtAddressLine2;
                $eviction->amt_greater_than_zero = $isAmtGreaterThanZero;
                $eviction->owner_name = $ownerName;
                $eviction->magistrate_id = $magistrateId;
                $eviction->plantiff_name = $plantiffName;
                $eviction->plantiff_phone = $plantiffPhone;
                $eviction->plantiff_address_line_1 = $plantiffAddress1;
                $eviction->plantiff_address_line_2 = $plantiffAddress2;
                $eviction->pm_name = $_POST['pm_name'];
                $eviction->pm_phone = $_POST['pm_phone'];
                $eviction->verify_name = $verifyName;
                $eviction->unit_num = $_POST['unit_number'];
                $eviction->user_id = Auth::user()->id;
                $eviction->docket_number = 'MJ-' . $_POST['docket_number_1'] . '-LT-' . $docketNumber2 . '-' . $_POST['docket_number_3'];
                $eviction->date_of_oop = date("m/d/Y");
                $eviction->court_filing_fee = '0';
                $eviction->filing_fee = number_format($filingFee, 2);
                $eviction->pm_company_name = $_POST['other_name'];
                $eviction->file_type = 'oop';
                $eviction->is_extra_files = $_POST['is_extra_filing'];
                $eviction->is_online_filing = $isOnline;
                if ($_POST['file_type'] == 'oopA') {
                    $eviction->is_in_person_filing = 1;
                } else {
                    $eviction->is_in_person_filing = 0;
                }

                $eviction->save();

                $evictionId = $eviction->id;

                $signature = new Signature();
                $signature->eviction_id = $evictionId;
                $signature->signature = $_POST['signature_source'];

                $signature->save();

                if (isset($_POST['file_address_ids'])) {
                    foreach ($_POST['file_address_ids'] as $fileAddressId) {
                        DB::table('file_addresses')
                            ->where('id', $fileAddressId)
                            ->update(['filing_id' => $evictionId]);
                    }
                }

                try {
                    $token = $_POST['stripeToken'];
                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = serialize($_POST);

                    $errorMsg->save();

                    if (strpos(Auth::user()->email, 'slatehousegroup') === false && strpos(Auth::user()->email, 'home365.co') === false && strpos(Auth::user()->email, 'elite.team') === false) {
                        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
                        if (isset($_POST['file_type']) == 'oopA') {
                            $amount = $filingFee + 275.00;
                        } else {
                            $amount = $filingFee + 25.00;
                        }
                    } else {
                        Stripe::setApiKey(env('STRIPE_SECRET_TEST_KEY'));
                        $amount = $filingFee + 25.00;
                    }
                    $stringAmt = strval($amount);
                    $stringAmt = str_replace('.', '', $stringAmt);
                    $integerAmt = intval($stringAmt);

                    $errorMsg = new ErrorLog();
                    $errorMsg->payload ='Integer Amount OOP: ' . $integerAmt;
                    $errorMsg->save();

                    \Stripe\Charge::create([
                        'amount' => $integerAmt,
                        'currency' => 'usd',
                        'description' => 'CourtZip',
                        'source' => $token,
                    ]);
                } catch ( Exception $e ) {
                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

                    $errorMsg->save();
                    $mailer->sendMail('andrew.gaidis@gmail.com', 'OOP Error', $e->getMessage(),  $e->getMessage() );
                }

                $notify = new NotificationController($courtNumber, Auth::user()->email);
                $notify->notifyAdmin();
                if ($isOnline === 1) {
                    $notify->notifyJudge();
                }
                $notify->notifyMaker();

                Session::flash('status', 'Your OOP has been successfully made! You can see its progress in the table below.');

                return 'success';

            } catch ( Exception $e ) {
                $errorMsg = new ErrorLog();
                $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

                $errorMsg->save();
                $mailer->sendMail('andrew.gaidis@gmail.com', 'OOP Error', $e->getMessage(),  $e->getMessage() );
            }
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            $mailer->sendMail('andrew.gaidis@gmail.com', 'OOP Error', $e->getMessage(),  $e->getMessage() );
            return redirect('dashboard')->with('status','Your OOP has been successfully made! You can see its progress in the table below.');
        }
    }
}
