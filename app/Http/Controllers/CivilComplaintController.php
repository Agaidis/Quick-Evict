<?php

namespace App\Http\Controllers;

use App\CivilRelief;
use App\CivilUnique;
use App\Classes\Mailer;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\GeoLocation;
use GMaps;
use App\CourtDetails;
use App\Evictions;
use App\Signature;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;
use stdClass;
use App\PDF;
use Dompdf\Options;
use Dompdf\Dompdf;
use App\ErrorLog;
use Illuminate\Support\Facades\DB;

class CivilComplaintController extends Controller
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
            $removeValues = [' ', '$', ','];
            $magistrateId = str_replace('magistrate_' , '', $_POST['court_number']);
            $courtDetails = CourtDetails::where('magistrate_id', $magistrateId)->first();
            $geoDetails = GeoLocation::where('magistrate_id', $magistrateId)->first();
            $civilDetails = CivilUnique::where('court_details_id', $courtDetails->id)->first();

            $pdfHtml = PDF::where('name', 'civil')->value('html');
            $filingFee = '';
            $totalJudgment = str_replace($removeValues,['', '', ''], $_POST['total_judgment']);
            $pdfEditor = new PDFEditController();
            $evictionData = new stdClass();
            $additionalTenantAmt = 0;

            $plaintiffName = $_POST['owner_name'];
            $plaintiffPhone = $_POST['owner_phone'];
            $plaintiffAddress1 = $_POST['owner_address_1'];
            $plaintiffAddress2 = $_POST['owner_address_2'];


            $tenantName = implode(', ', $_POST['tenant_name']);

            $tenantNum = (int)$_POST['tenant_num'];

            if ($tenantNum > 1) {
                if ($_POST['delivery_type'] == 'mail') {
                    if ($totalJudgment <= 500) {
                        $filingFee = $civilDetails->under_500_2_def_mail;
                    } else if ($totalJudgment > 500 && $totalJudgment <= 2000) {
                        $filingFee = $civilDetails->btn_500_2000_2_def_mail;
                    } else if ($totalJudgment > 2000 && $totalJudgment < 4001) {
                        $filingFee = $civilDetails->btn_2000_4000_2_def_mail;
                    } else if ($totalJudgment > 4000 && $totalJudgment < 12001) {
                        $filingFee = $civilDetails->btn_4000_12000_2_def_mail;
                    }

                    if ($tenantNum > 2) {
                        if ($courtDetails->civil_mail_additional_tenant_fee != '' && $courtDetails->civil_mail_additional_tenant_fee != 0 ) {
                            $additionalTenantAmt = $courtDetails->civil_mail_additional_tenant_fee;
                        }
                    }
                } else if ($_POST['delivery_type'] == 'constable') {
                    if ($totalJudgment <= 500) {
                        $filingFee = $civilDetails->under_500_2_def_constable;
                    } else if ($totalJudgment > 500 && $totalJudgment <= 2000) {
                        $filingFee = $civilDetails->btn_500_2000_2_def_constable;
                    } else if ($totalJudgment > 2000 && $totalJudgment < 4001) {
                        $filingFee = $civilDetails->btn_2000_4000_2_def_constable;
                    } else if ($totalJudgment > 4000 && $totalJudgment < 12001) {
                        $filingFee = $civilDetails->btn_4000_12000_2_def_constable;
                    }

                    if ($tenantNum > 2) {
                        if ($courtDetails->civil_constable_additional_tenant_fee != '' && $courtDetails->civil_constable_additional_tenant_fee != 0 ) {
                            $additionalTenantAmt = $courtDetails->civil_constable_additional_tenant_fee;
                        }
                    }
                }


            } else {
                if ($_POST['delivery_type'] == 'mail') {
                    if ($totalJudgment <= 500) {
                        $filingFee = $civilDetails->under_500_1_def_constable;
                    } else if ($totalJudgment > 500 && $totalJudgment <= 2000) {
                        $filingFee = $civilDetails->btn_500_2000_1_def_constable;
                    } else if ($totalJudgment > 2000 && $totalJudgment < 4001) {
                        $filingFee = $civilDetails->btn_2000_4000_1_def_constable;
                    } else if ($totalJudgment > 4000 && $totalJudgment < 12001) {
                        $filingFee = $civilDetails->btn_4000_12000_1_def_constable;
                    }
                } else if ($_POST['delivery_type'] == 'constable') {
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

            $totalJudgment = number_format($totalJudgment, 2);
            $filingFee = number_format($filingFee, 2) + $additionalTenantAmt;

            $plaintiffAddress = $plaintiffName .'<br>'. $plaintiffAddress1 .'<br>'. $plaintiffAddress2 .'<br>'. $plaintiffPhone;
            $defendantAddress = $tenantName . '<br>' . $_POST['civil_defendant_address_1'] . ', ' . $_POST['unit_number'] .'<br>'. $_POST['civil_defendant_address_2'];

            $evictionData->id = '-1';
            $evictionData->plantiff_name = $plaintiffName;
            $evictionData->filing_fee = $filingFee;
            $evictionData->total_judgment = $totalJudgment;
            $evictionData->court_address_line_1 = $geoDetails->address_line_one;
            $evictionData->court_address_line_2 = $geoDetails->address_line_two;
            $evictionData->claim_description = $_POST['claim_description'];

            $pdfHtml = $pdfEditor->globalHtmlAttributes($pdfHtml, $courtDetails, $plaintiffAddress, $defendantAddress, $_POST['signature_source'], $evictionData);
            $pdfHtml = $pdfEditor->localCivilAttributes($pdfHtml, $evictionData);
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
            $domPdf->stream("sample_civil.pdf", array("Attachment" => 0));

        } catch ( Exception $e) {

            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();

            $mailer->sendMail('andrew.gaidis@gmail.com', 'Civil Preview Error', $e->getMessage(),  $e->getMessage() );
        }

    }

    public function formulatePDF()
    {
        $mailer = new Mailer();
        try {

            $removeValues = [' ', '$', ','];
            $magistrateId = str_replace('magistrate_' , '', $_POST['court_number']);
            $courtDetails = CourtDetails::where('magistrate_id', $magistrateId)->first();
            $geoDetails = GeoLocation::where('magistrate_id', $magistrateId)->first();
            $civilDetails = CivilUnique::where('court_details_id', $courtDetails->id)->first();
            $totalJudgment = str_replace($removeValues,['', '', ''], $_POST['total_judgment']);
            $isOnline = 0;

            if ($courtDetails->online_submission == 'of') {
                $isOnline = 1;
                $status = 'Civil Submitted, $$ needs del';
            } else if ($courtDetails->online_submission === 'otm' ) {
                $status = 'Civil, to be mailed';
            } else if ($courtDetails->online_submission === 'otp') {
                $status = 'Civil Submitted, $$ & file needs DEL';
            } else {
                $status = '';
            }

            $courtNumber = $courtDetails->court_number;

            $courtAddressLine1 = $geoDetails->address_line_one;
            $courtAddressLine2 = $geoDetails->address_line_two;

            $ownerName = $_POST['owner_name'];
            $verifyName = $_POST['owner_name'];
            $plantiffName = $_POST['owner_name'];
            $plantiffPhone = $_POST['owner_phone'];
            $plantiffAddress1 = $_POST['owner_address_1'];
            $plantiffAddress2 = $_POST['owner_address_2'];

            $tenantName = implode(', ', $_POST['tenant_name']);

            $tenantNum = (int)$_POST['tenant_num'];

            if ($tenantNum > 1) {
                if ($_POST['delivery_type'] == 'mail') {
                    if ($totalJudgment <= 500) {
                        $filingFee = $civilDetails->under_500_2_def_mail;
                    } else if ($totalJudgment > 500 && $totalJudgment <= 2000) {
                        $filingFee = $civilDetails->btn_500_2000_2_def_mail;
                    } else if ($totalJudgment > 2000 && $totalJudgment < 4001) {
                        $filingFee = $civilDetails->btn_2000_4000_2_def_mail;
                    } else if ($totalJudgment > 4000 && $totalJudgment < 12001) {
                        $filingFee = $civilDetails->btn_4000_12000_2_def_mail;
                    }
                } else if ($_POST['delivery_type'] == 'constable') {
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
                if ($_POST['delivery_type'] == 'mail') {
                    if ($totalJudgment <= 500) {
                        $filingFee = $civilDetails->under_500_1_def_constable;
                    } else if ($totalJudgment > 500 && $totalJudgment <= 2000) {
                        $filingFee = $civilDetails->btn_500_2000_1_def_constable;
                    } else if ($totalJudgment > 2000 && $totalJudgment < 4001) {
                        $filingFee = $civilDetails->btn_2000_4000_1_def_constable;
                    } else if ($totalJudgment > 4000 && $totalJudgment < 12001) {
                        $filingFee = $civilDetails->btn_4000_12000_1_def_constable;
                    }
                } else if ($_POST['delivery_type'] == 'constable') {
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

            $totalJudgment = number_format($totalJudgment, 2);

            if (isset($_POST['distance_fee'])) {
                $filingFee = $filingFee + (float)$_POST['distance_fee'];
            }

            $filingFee = number_format($filingFee, 2);


            $defendantState = $_POST['state'];
            $defendantZipcode = $_POST['zipcode'];
            $defendanthouseNum = $_POST['houseNum'];
            $defendantStreetName = $_POST['streetName'];
            $defendantTown = $_POST['town'];
            $civilDefendantAddress1 = $_POST['civil_defendant_address_1'];
            $civilDefendantAddress2 = $_POST['civil_defendant_address_2'];

            $eviction = new Evictions();
            $eviction->status = $status;
            $eviction->property_address = $defendanthouseNum.' '.$defendantStreetName.'-1'.$defendantTown .', ' . $defendantState.' '.$defendantZipcode;
            $eviction->tenant_name = $tenantName;
            $eviction->defendant_state = $civilDefendantAddress1;
            $eviction->defendant_zipcode = $civilDefendantAddress2;
            $eviction->defendant_house_num = $defendanthouseNum;
            $eviction->defendant_street_name = $defendantStreetName;
            $eviction->defendant_town = $defendantTown;
            $eviction->total_judgement = $totalJudgment;
            $eviction->pdf_download = 'true';
            $eviction->court_number = $courtNumber;
            $eviction->court_address_line_1 = $courtAddressLine1;
            $eviction->court_address_line_2 = $courtAddressLine2;
            $eviction->owner_name = $ownerName;
            $eviction->magistrate_id = $magistrateId;
            $eviction->unit_num = $_POST['unit_number'];
            $eviction->plantiff_name = $plantiffName;
            $eviction->plantiff_phone = $plantiffPhone;
            $eviction->plantiff_address_line_1 = $plantiffAddress1;
            $eviction->plantiff_address_line_2 = $plantiffAddress2;
            $eviction->verify_name = $verifyName;
            $eviction->user_id = Auth::user()->id;
            $eviction->court_filing_fee = '0';
            $eviction->claim_description = $_POST['claim_description'];
            $eviction->file_type = 'civil complaint';
            $eviction->civil_delivery_type = $_POST['delivery_type'];
            $eviction->filing_fee = $filingFee;
            $eviction->is_extra_files = 1;
            $eviction->is_online_filing = $isOnline;

            $eviction->save();

            $evictionId = $eviction->id;

            $signature = new Signature();
            $signature->eviction_id = $evictionId;
            $signature->signature = $_POST['signature_source'];

            $signature->save();

            for ($i = 1; $i <= count($_POST['tenant_name']); $i++) {
                $civilRelief = new CivilRelief();

                $civilRelief->name = $_POST['tenant_name'][$i - 1];
                $civilRelief->filing_id = $evictionId;
                $civilRelief->military_awareness = $_POST['tenant_military_' . $i];
                $civilRelief->military_description = $_POST['tenant_military_explanation_' . $i];

                $civilRelief->save();
            }

            if (isset($_POST['file_address_ids'])) {
                foreach ($_POST['file_address_ids'] as $fileAddressId) {
                    DB::table('file_addresses')
                        ->where('id', $fileAddressId)
                        ->update(['filing_id' => $evictionId]);
                }
            }



            try {
                $token = $_POST['stripeToken'];

                if (strpos(Auth::user()->email, 'slatehousegroup') === false && strpos(Auth::user()->email, 'home365.co') === false && strpos(Auth::user()->email, 'elite.team') === false) {
                    Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
                    $amount = $filingFee + 25;
                } else {
                    Stripe::setApiKey(env('STRIPE_SECRET_TEST_KEY'));
                    $amount = $filingFee + 25;
                }
                $stringAmt = strval($amount);
                $stringAmt = str_replace('.', '', $stringAmt);
                $integerAmt = intval($stringAmt);

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

            Session::flash('status', 'Your Civil Complaint has been successfully made! You can see its progress in the table below.');

            return 'success';

        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }

    }
}
