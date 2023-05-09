<?php

namespace App\Http\Controllers;

use App\CivilRelief;
use App\ErrorLog;
use App\PDF;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use App\GeoLocation;
use GMaps;
use App\CourtDetails;
use App\Evictions;
use App\Signature;
use App\Classes\Mailer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use stdClass;
use Stripe\Stripe;
use GuzzleHttp;



class EvictionController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function delete() {
        try {
            $dbId = Evictions::where('id', $_POST['id'])->value('id');
            Evictions::destroy($dbId);
            return $dbId;
        } catch (\Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'failed';
        }
    }

    public function showSamplePDF() {
        $mailer = new Mailer();

        try {
            $removeValues = ['$', ',', ' '];
            $magistrateId = str_replace('magistrate_' , '', $_POST['court_number']);
            $courtDetails = CourtDetails::where('magistrate_id', $magistrateId)->first();
            $geoDetails = GeoLocation::where('magistrate_id', $magistrateId)->first();
            $pdfHtml = PDF::where('name', 'ltc')->value('html');
            $pdfEditor = new PDFEditController();
            $evictionData = new stdClass();

            $additionalRentAmt = str_replace($removeValues, '', $_POST['additional_rent_amt']);
            $attorneyFees = str_replace($removeValues, '', $_POST['attorney_fees']);
            $damageAmt = str_replace($removeValues, '', $_POST['damage_amt']);
            $dueRent = str_replace($removeValues, '', $_POST['due_rent']);
            $securityDeposit = str_replace($removeValues, '', $_POST['security_deposit']);
            $monthlyRent = str_replace($removeValues, '', $_POST['monthly_rent']);
            $unjustDamages = str_replace($removeValues, '', $_POST['unjust_damages']);

            $tenantName = implode(', ', $_POST['tenant_name']);

            $pmName = $_POST['pm_name'];

            if ($_POST['rented_by_val'] == 'rentedByOwner') {
                $verifyName = $_POST['owner_name'];
                $plaintiffName = $_POST['owner_name'];
                $plaintiffPhone = $_POST['owner_phone'];
                $plaintiffAddress1 = $_POST['owner_address_1'];
                $plaintiffAddress2 = $_POST['owner_address_2'];
            } else {
                $verifyName = $pmName;
                $plaintiffName = $_POST['other_name'] . ' on behalf of ' . $_POST['owner_name'];
                $plaintiffPhone = $_POST['pm_phone'];
                $plaintiffAddress1 = $_POST['pm_address_1'];
                $plaintiffAddress2 = $_POST['pm_address_2'];
            }

            $additionalTenantAmt = 1;
            $additionalTenantFee = 0;

            if ($_POST['tenant_num'] == "2") {
                $upTo2000 = $courtDetails->two_defendant_up_to_2000;
                $btn20014000 = $courtDetails->two_defendant_between_2001_4000;
                $greaterThan4000 = $courtDetails->two_defendant_greater_than_4000;
            } else if ($_POST['tenant_num'] == "1") {
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

            $tenantNum = (int)$_POST['tenant_num'];

            if ($tenantNum > 3) {
                $multiplyBy = $tenantNum - 3;
                $additionalTenantFee = (float)$additionalTenantAmt * $multiplyBy;
            }

            $isIsResidential = $_POST['lease_type'] == 'isResidential' ? true : false;
            $isNoQuitNotice = $_POST['quit_notice'] == 'no_quit_notice' ? true : false;
            $isUnsatisfiedLease = isset($_POST['unsatisfied_lease']) ? true : false;
            $isBreachedConditionsLease = isset($_POST['breached_conditions_lease']) ? true : false;
            $breachedDetails = isset($_POST['breached_details']) ? $_POST['breached_details'] : '';
            $isLeaseEnded = isset($_POST['term_lease_ended']) ? true : false;
            $isAdditionalRent = $_POST['addit_rent'] == 'yes' ? true : false;
            $isDeterminationRequest = isset($_POST['is_determination_request']) ? true : false;
            $isAbandoned = isset($_POST['is_abandoned']) ? true : false;

            $totalFees = (float)$additionalRentAmt + (float)$attorneyFees + (float)$dueRent + (float)$unjustDamages + (float)$damageAmt;


            $noCommaTotalFees = str_replace(',','', $totalFees);

            $totalFees = number_format($totalFees, 2);

            $plaintiffAddress = $plaintiffName .'<br>'. $plaintiffAddress1 .'<br>'. $plaintiffAddress2 .'<br>'. $plaintiffPhone;


            //Tenant lives at incident address, do not change anything
            if ($_POST['does_tenant_reside'] === 'tenantResides') {
                $defendantAddress = $tenantName . '<br>' . $_POST['houseNum'] . ' ' . $_POST['streetName'] . ', ' . $_POST['incident_addit_address_detail'] .'<br> '. $_POST['town'] .', '. $_POST['state'] .' '. $_POST['zipcode'];
                $defendantAddress2 = $_POST['houseNum'] . ' ' . $_POST['streetName'] .' '. $_POST['incident_addit_address_detail'] . '<span style="position:absolute; margin-top:-10px;">'. $_POST['town'] .', ' . $_POST['state'] .' '. $_POST['zipcode'] . '</span>';
            } else if ($_POST['does_tenant_reside'] === 'tenantDoesNotReside') {
                $defendantAddress = $tenantName . '<br>' . $_POST['residedHouseNum'] . ' ' . $_POST['residedStreetName'] . ', ' . $_POST['tenant_addit_address_detail'] .'<br> '. $_POST['residedTown'] .', '. $_POST['residedState'] .' '. $_POST['residedZipcode'];
                $defendantAddress2 = $_POST['houseNum'] . ' ' . $_POST['streetName'] .' '. $_POST['incident_addit_address_detail'] . '<span style="position:absolute; margin-top:-10px;">'. $_POST['town'] .', ' . $_POST['state'] .' '. $_POST['zipcode'] . '</span>';
            }

            if ($noCommaTotalFees < 2000) {
                $filingFee = $upTo2000 + $additionalTenantFee;
            } else if ($noCommaTotalFees >= 2000 && $noCommaTotalFees <= 4000) {
                $filingFee = $btn20014000 + $additionalTenantFee;
            } else if ($noCommaTotalFees > 4000) {
                $filingFee = $greaterThan4000 + $additionalTenantFee;
            } else {
                $filingFee = 'Didnt Work';
            }

            if (isset($_POST['distance_fee'])) {
                $filingFee = $filingFee + (float)$_POST['distance_fee'];
            }

            $filingFee = number_format($filingFee, 2);

            $isAmtGreaterThanZero = $noCommaTotalFees > 0 ? true : false;


            $evictionData->id = '-1';

            $evictionData->plantiff_name = $plaintiffName;
            $evictionData->court_address_line_1 = $geoDetails->address_line_one;
            $evictionData->court_address_line_2 = $geoDetails->address_line_two;
            $evictionData->total_judgement = $totalFees;
            $evictionData->filing_fee = number_format($filingFee, 2);
            $evictionData->attorney_fees = $_POST['attorney_fees'];
            $evictionData->due_rent = $dueRent;
            $evictionData->damage_amt = $damageAmt;
            $evictionData->unjust_damages = $unjustDamages;
            $evictionData->additional_rent_amt = $additionalRentAmt;
            $evictionData->security_deposit = $securityDeposit;
            $evictionData->monthly_rent = $monthlyRent;
            $evictionData->breached_details = $breachedDetails;
            $evictionData->property_damage_details = $_POST['damages_details'];
            $evictionData->verify_name = $verifyName;
            $evictionData->is_residential = $isIsResidential;
            $evictionData->is_abandoned = $isAbandoned;
            $evictionData->amt_greater_than_zero = $isAmtGreaterThanZero;
            $evictionData->is_additional_rent = $isAdditionalRent;
            $evictionData->no_quit_notice = $isNoQuitNotice;
            $evictionData->unsatisfied_lease = $isUnsatisfiedLease;
            $evictionData->breached_conditions_lease = $isBreachedConditionsLease;
            $evictionData->lease_ended = $isLeaseEnded;
            $evictionData->is_determination_request = $isDeterminationRequest;

            $pdfHtml = $pdfEditor->globalHtmlAttributes($pdfHtml, $courtDetails, $plaintiffAddress, $defendantAddress, $_POST['signature_source'], $evictionData);
            $pdfHtml = $pdfEditor->localLTCAttributes($pdfHtml, $evictionData, $defendantAddress2);
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
        } catch ( Exception $e) {

            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();

            $mailer->sendMail('andrew.gaidis@gmail.com', 'Civil Preview Error', $e->getMessage(),  $e->getMessage() );
        }
    }

    public function formulatePDF() {


        $mailer = new Mailer();
        try {

            if (isset($_POST['h-captcha-response'])) {
                $data = array(
                    'secret' => "0xeCB96921f42C7E0b64ec07D6B143F990A7F6B7a7",
                    'response' => $_POST['h-captcha-response']
                );
                $verify = curl_init();
                curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
                curl_setopt($verify, CURLOPT_POST, true);
                curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
                $verifyResponse = curl_exec($verify);
                $responseData = json_decode($verifyResponse);

                if ($responseData->success) {
                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = 'success! ' . serialize($verifyResponse);
                    $errorMsg->save();
                } else {
                    Session::flash('status', 'The captcha was not filled correctly and therefore the filing was not processed.');

                    return 'success';
                }
            }

            $removeValues = ['$', ',', ' '];
            $magistrateId = str_replace('magistrate_' , '', $_POST['court_number']);
            $courtDetails = CourtDetails::where('magistrate_id', $magistrateId)->first();
            $geoDetails = GeoLocation::where('magistrate_id', $magistrateId)->first();
            $isOnline = 0;

            if ($courtDetails->online_submission == 'of') {
                $isOnline = 1;
                $status = 'LTC Submitted, $$ needs del';
            } else if ($courtDetails->online_submission === 'otm' ) {
                $status = 'LTC, to be mailed';
            } else if ($courtDetails->online_submission === 'otp') {
                $status = 'LTC Submitted, $$ & file needs DEL';
            } else {
                $status = '';
            }

            $courtNumber = $courtDetails->court_number;

            $courtAddressLine1 = $geoDetails->address_line_one;
            $courtAddressLine2 = $geoDetails->address_line_two;

            $additionalRentAmt = str_replace($removeValues, '', $_POST['additional_rent_amt']);
            $attorneyFees = str_replace($removeValues, '', $_POST['attorney_fees']);
            $damageAmt = str_replace($removeValues, '', $_POST['damage_amt']);
            $dueRent = str_replace($removeValues, '', $_POST['due_rent']);
            $securityDeposit = str_replace($removeValues, '', $_POST['security_deposit']);
            $monthlyRent = str_replace($removeValues, '', $_POST['monthly_rent']);
            $unjustDamages = str_replace($removeValues, '', $_POST['unjust_damages']);

            $tenantName = implode(', ', $_POST['tenant_name']);

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

            $additionalTenantAmt = 1;
            $additionalTenantFee = 0;

            if ($_POST['tenant_num'] == "2") {
                $upTo2000 = $courtDetails->two_defendant_up_to_2000;
                $btn20014000 = $courtDetails->two_defendant_between_2001_4000;
                $greaterThan4000 = $courtDetails->two_defendant_greater_than_4000;
            } else if ($_POST['tenant_num'] == "1") {
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
            $tenantNum = (int)$_POST['tenant_num'];

            if ($tenantNum > 3) {
                $multiplyBy = $tenantNum - 3;
                $additionalTenantFee = (float)$additionalTenantAmt * $multiplyBy;
            }

            $isIsResidential = $_POST['lease_type'] == 'isResidential' ? true : false;
            $isNoQuitNotice = $_POST['quit_notice'] == 'no_quit_notice' ? true : false;
            $isUnsatisfiedLease = isset($_POST['unsatisfied_lease']) ? true : false;
            $isBreachedConditionsLease = isset($_POST['breached_conditions_lease']) ? true : false;
            $breachedDetails = isset($_POST['breached_details']) ? $_POST['breached_details'] : '';
            $isLeaseEnded = isset($_POST['term_lease_ended']) ? true : false;
            $isAdditionalRent = $_POST['addit_rent'] == 'yes' ? true : false;
            $isDeterminationRequest = isset($_POST['is_determination_request']) ? true : false;
            $isAbandoned = isset($_POST['is_abandoned']) ? true : false;

            $totalFees = (float)$additionalRentAmt + (float)$attorneyFees + (float)$dueRent + (float)$unjustDamages + (float)$damageAmt;


            $noCommaTotalFees = str_replace(',','', $totalFees);

            $totalFees = number_format($totalFees, 2);

            if ($noCommaTotalFees < 2000) {
                $filingFee = $upTo2000 + $additionalTenantFee;
            } else if ($noCommaTotalFees >= 2000 && $noCommaTotalFees <= 4000) {
                $filingFee = $btn20014000 + $additionalTenantFee;
            } else if ($noCommaTotalFees > 4000) {
                $filingFee = $greaterThan4000 + $additionalTenantFee;
            } else {
                $filingFee = 'Didnt Work';
            }

            if (isset($_POST['distance_fee'])) {
                $filingFee = $filingFee + (float)$_POST['distance_fee'];
            }

            $isAmtGreaterThanZero = $noCommaTotalFees > 0 ? true : false;

            $filingFee = number_format($filingFee, 2);

            $defendantState = $_POST['state'];
            $defendantZipCode = $_POST['zipcode'];
            $defendantHouseNum = $_POST['houseNum'];
            $defendantStreetName = $_POST['streetName'];
            $defendantTown = $_POST['town'];
            $reside = 'yes';


            //Tenant lives at incident address, do not change anything
            if ($_POST['does_tenant_reside'] === 'tenantResides') {
                $reside = 'yes';
                $defendantResidedState = $_POST['state'];
                $defendantResidedZipcode = $_POST['zipcode'];
                $defendantResidedHouseNum = $_POST['houseNum'];
                $defendantResidedStreetName = $_POST['streetName'];
                $defendantResidedTown = $_POST['town'];
            } else if ($_POST['does_tenant_reside'] === 'tenantDoesNotReside') {
                $reside = 'no';
                $defendantResidedState = $_POST['residedState'];
                $defendantResidedZipcode = $_POST['residedZipcode'];
                $defendantResidedHouseNum = $_POST['residedHouseNum'];
                $defendantResidedStreetName = $_POST['residedStreetName'];
                $defendantResidedTown = $_POST['residedTown'];
            }




            try {
                $eviction = new Evictions();
                $eviction->status = $status;
                $eviction->total_judgement = $totalFees;
                $eviction->property_address = $defendantHouseNum.' '.$defendantStreetName.'-1'.$defendantTown .',' . $defendantState.' '.$defendantZipCode;
                $eviction->is_resided = $reside;
                $eviction->resided_address = $defendantResidedHouseNum.' '.$defendantResidedStreetName.'-1'.$defendantResidedTown .',' . $defendantResidedState.' '.$defendantResidedZipcode;
                $eviction->tenant_name = $tenantName;
                $eviction->court_filing_fee = $filingFee;
                $eviction->pdf_download = 'true';
                $eviction->court_number = $courtNumber;
                $eviction->court_address_line_1 = $courtAddressLine1;
                $eviction->court_address_line_2 = $courtAddressLine2;
                $eviction->owner_name = $ownerName;
                $eviction->magistrate_id = $magistrateId;
                $eviction->attorney_fees = $attorneyFees;
                $eviction->damage_amt = $damageAmt;
                $eviction->due_rent = $dueRent;
                $eviction->security_deposit = $securityDeposit;
                $eviction->monthly_rent = $monthlyRent;
                $eviction->unjust_damages = $unjustDamages;
                $eviction->breached_details = $breachedDetails;
                $eviction->property_damage_details = $_POST['damages_details'];
                $eviction->is_residential = $isIsResidential;
                $eviction->no_quit_notice = $isNoQuitNotice;
                $eviction->unsatisfied_lease = $isUnsatisfiedLease;
                $eviction->breached_conditions_lease = $isBreachedConditionsLease;
                $eviction->amt_greater_than_zero = $isAmtGreaterThanZero;
                $eviction->lease_ended = $isLeaseEnded;
                $eviction->is_additional_rent = $isAdditionalRent;
                $eviction->defendant_state = $defendantState;
                $eviction->defendant_zipcode = $defendantZipCode;
                $eviction->defendant_house_num = $defendantHouseNum;
                $eviction->defendant_street_name = $defendantStreetName;
                $eviction->defendant_town = $defendantTown;
                $eviction->filing_fee = $filingFee;
                $eviction->is_abandoned = $isAbandoned;
                $eviction->is_determination_request = $isDeterminationRequest;
                $eviction->unit_num = $_POST['incident_addit_address_detail'];
                $eviction->additional_rent_amt = $_POST['additional_rent_amt'];
                $eviction->plantiff_name = $plantiffName;
                $eviction->plantiff_phone = $plantiffPhone;
                $eviction->plantiff_address_line_1 = $plantiffAddress1;
                $eviction->plantiff_address_line_2 = $plantiffAddress2;
                $eviction->verify_name = $verifyName;
                $eviction->user_id = Auth::user()->id;
                $eviction->file_type = 'eviction';
                $eviction->is_extra_files = 1;//$_POST['is_extra_filing'];
                $eviction->is_online_filing = $isOnline;
                if ($_POST['file_type'] == 'ltcA') {
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

                    $payType = Auth::user()->pay_type;

                     if ($payType == 'full_payment') {
                         $token = $_POST['stripeToken'];
                         Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

                        if ($_POST['file_type'] == 'ltcA') {
                            $amount = $filingFee + 250.00;
                        } else {
                            $amount = $filingFee + 25.00;
                        }

                        $stringAmt = strval($amount);

                         $errorMsg = new ErrorLog();
                         $errorMsg->payload = 'string Amt 1: ' . $stringAmt;
                         $errorMsg->save();

                        $stringAmt = str_replace('.', '', $stringAmt);

                         $errorMsg = new ErrorLog();
                         $errorMsg->payload = 'string Amt 2: ' . $stringAmt;
                         $errorMsg->save();

                        $integerAmt = intval($stringAmt);


                         $errorMsg = new ErrorLog();
                         $errorMsg->payload = 'integer Amt: ' . $integerAmt;
                         $errorMsg->save();

                        \Stripe\Charge::create([
                            'amount' => $integerAmt,
                            'currency' => 'usd',
                            'description' => 'CourtZip',
                            'source' => $token,
                        ]);
                    }

                } catch ( Exception $e ) {
                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

                    $errorMsg->save();
                    $mailer->sendMail('andrew.gaidis@gmail.com', 'LTC Error', $e->getMessage(),  $e->getMessage() );
                }

                try {
                    $notify = new NotificationController($courtNumber, Auth::user()->email);
                    $notify->notifyAdmin();
                    if ($isOnline === 1) {
                        $notify->notifyJudge();
                    }
                    $notify->notifyMaker();
                } catch ( Exception $e) {
                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
                    $errorMsg->save();

                    $mailer->sendMail('andrew.gaidis@gmail.com', 'Notification Error' . Auth::user()->id, $e->getMessage(),  $e->getMessage());
                }


                Session::flash('status', 'Your LTC has been successfully made! You can see its progress in the table below.');

                return 'success';

            } catch ( \Exception $e) {
                $errorMsg = new ErrorLog();
                $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
                $errorMsg->save();
            }
        } catch ( \Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
            $errorMsg->save();
        }
    }

    public function getDigitalSignature() {
        try {
            $courtNumber = explode('_', $_POST['courtNumber']);
            $isDigitalSignature = CourtDetails::where('magistrate_id', $courtNumber[1])->get();
            return $isDigitalSignature;
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
            $errorMsg->save();
        }
    }
}