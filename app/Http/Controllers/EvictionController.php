<?php

namespace App\Http\Controllers;

use App\ErrorLog;
use App\PDF;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\GeoLocation;
use GMaps;
use App\CourtDetails;
use App\Evictions;
use App\Signature;
use App\Classes\Mailer;
use Illuminate\Support\Facades\Log;
use stdClass;
use Stripe\Stripe;



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
        Log::info('Deleting an Eviction');
        Log::info(Auth::User()->id);
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
            $removeValues = ['$', ','];
            $magistrateId = str_replace('magistrate_' , '', $_POST['court_number']);
            $courtDetails = CourtDetails::where('magistrate_id', $magistrateId)->first();
            $geoDetails = GeoLocation::where('magistrate_id', $magistrateId)->first();
            $pdfHtml = PDF::where('name', 'ltc')->value('html');
            $pdfEditor = new PDFEditController();
            $evictionData = new stdClass();

            $courtAddressLine1 = $geoDetails->address_line_one;
            $courtAddressLine2 = $geoDetails->address_line_two;

            $additionalRentAmt = str_replace($removeValues, '', $_POST['additional_rent_amt']);

            //Attorney Fees
            $attorneyFees = $_POST['attorney_fees'];
            $attorneyFees = str_replace($removeValues, '', $attorneyFees);

            $damageAmt = $_POST['damage_amt'];
            $damageAmt = str_replace($removeValues, '', $damageAmt);

            $dueRent = $_POST['due_rent'];
            $dueRent = str_replace($removeValues, '', $dueRent);

            $securityDeposit = $_POST['security_deposit'];
            $securityDeposit = str_replace($removeValues, '', $securityDeposit);

            $monthlyRent = $_POST['monthly_rent'];
            $monthlyRent = str_replace($removeValues, '', $monthlyRent);

            $unjustDamages = $_POST['unjust_damages'];
            $unjustDamages = str_replace($removeValues, '', $unjustDamages);

            $tenantName = implode(', ', $_POST['tenant_name']);

            $pmName = $_POST['pm_name'];
            $ownerName = $_POST['owner_name'];



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

            $isIsResidential = false;
            $isNoQuitNotice = false;
            $isUnsatisfiedLease = false;
            $isBreachedConditionsLease = false;
            $isAmtGreaterThanZero = false;
            $isLeaseEnded = false;
            $isAdditionalRent = false;
            $isAbandoned = false;
            $isDeterminationRequest = false;

            //Lease Type
            $leaseType = $_POST['lease_type'];
            if ($leaseType == 'isResidential') {
                $isIsResidential = true;
            }

            //Notice Status
            $quitNotice = $_POST['quit_notice'];
            if ($quitNotice == 'no_quit_notice') {
                $isNoQuitNotice = true;
            }

            //Lease Status
            if (isset($_POST['unsatisfied_lease'])) {
                $isUnsatisfiedLease = true;
            }
            if (isset($_POST['breached_conditions_lease'])) {
                $isBreachedConditionsLease = true;
            }
            if (isset($_POST['breached_details'])) {
                $breachedDetails = $_POST['breached_details'];
            } else {
                $breachedDetails = '';
            }

            $propertyDamageDetails = $_POST['damages_details'];

            if (isset($_POST['term_lease_ended'])) {
                $isLeaseEnded = true;
            }

            if ($_POST['addit_rent'] == 'yes') {
                $isAdditionalRent = true;
            }

            if (isset($_POST['is_determination_request'])) {
                $isDeterminationRequest = true;
            }

            if (isset($_POST['is_abandoned'])) {
                $isAbandoned = true;
            }


            $defendantState = $_POST['state'];
            $defendantZipcode = $_POST['zipcode'];
            $defendanthouseNum = $_POST['houseNum'];
            $defendantStreetName = $_POST['streetName'];
            $defendantTown = $_POST['town'];

            if (is_numeric($_POST['additional_rent_amt'])) {
                $totalFees = (float)$additionalRentAmt + (float)$attorneyFees + (float)$dueRent + (float)$unjustDamages + (float)$damageAmt;
            } else {
                $totalFees = (float)$attorneyFees + (float)$dueRent + (float)$unjustDamages + (float)$damageAmt;
            }

            $noCommaTotalFees = str_replace(',','', $totalFees);

            $totalFees = number_format($totalFees, 2);

            $plaintiffAddress = $plaintiffName .'<br>'. $plaintiffAddress1 .'<br>'. $plaintiffAddress2 .'<br>'. $plaintiffPhone;
            $defendantAddress = $tenantName . '<br>' . $_POST['houseNum'] . ' ' . $_POST['streetName'] . ', ' . $_POST['unit_number'] .'<br> '. $_POST['town'] .', '. $_POST['state'] .' '. $_POST['zipcode'];
            $defendantAddress2 = $_POST['houseNum'] . ' ' . $_POST['streetName'] .' '. $_POST['unit_number'] . '<br><br><span style="position:absolute; margin-top:-10px;">'. $_POST['town'] .', ' . $_POST['state'] .' '. $_POST['zipcode'];

            if ($noCommaTotalFees < 2000) {
                $filingFee = $upTo2000 + $additionalTenantFee;
            } else if ($noCommaTotalFees >= 2000 && $noCommaTotalFees <= 4000) {
                $filingFee = $btn20014000 + $additionalTenantFee;
            } else if ($noCommaTotalFees > 4000) {
                $filingFee = $greaterThan4000 + $additionalTenantFee;
            } else {
                $filingFee = 'Didnt Work';
            }

            if ($noCommaTotalFees > 0) {
                $isAmtGreaterThanZero = true;
            }

            $evictionData->id = '-1';

            $evictionData->plantiff_name = $plaintiffName;
            $evictionData->court_address_line_1 = $geoDetails->address_line_one;
            $evictionData->court_address_line_2 = $geoDetails->address_line_two;
            $evictionData->total_judgement = $totalFees;
            $evictionData->filing_fee = number_format($filingFee, 2);
            $evictionData->attorney_fees = $_POST['attorney_fees'];
            $evictionData->judgment_amount = $_POST['judgment_amount'];
            $evictionData->due_rent = $dueRent;
            $evictionData->damage_amt = $damageAmt;
            $evictionData->unjust_damages = $unjustDamages;
            $evictionData->additional_rent_amt = $additionalRentAmt;
            $evictionData->security_deposit = $securityDeposit;
            $evictionData->monthly_rent = $monthlyRent;
            $evictionData->breached_details = $breachedDetails;
            $evictionData->property_damage_details = $propertyDamageDetails;
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
            $evictionData->costs_original_lt_proceeding = $_POST['costs_original_lt_proceeding'];

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

            $mailer->sendMail('andrew.gaidis@gmail.com', 'Civil Preview Error', $e->getMessage() );
        }
    }

    public function formulatePDF() {
        $mailer = new Mailer();
        try {
            $removeValues = ['$', ','];
            $magistrateId = str_replace('magistrate_' , '', $_POST['court_number']);
            $courtDetails = CourtDetails::where('magistrate_id', $magistrateId)->first();
            $geoDetails = GeoLocation::where('magistrate_id', $magistrateId)->first();

            $courtNumber = $courtDetails->court_number;

            $courtAddressLine1 = $geoDetails->address_line_one;
            $courtAddressLine2 = $geoDetails->address_line_two;

            $additionalRentAmt = str_replace($removeValues, '', $_POST['additional_rent_amt']);

            //Attorney Fees
            $attorneyFees = $_POST['attorney_fees'];
            $attorneyFees = str_replace($removeValues, '', $attorneyFees);

            $damageAmt = $_POST['damage_amt'];
            $damageAmt = str_replace($removeValues, '', $damageAmt);

            $dueRent = $_POST['due_rent'];
            $dueRent = str_replace($removeValues, '', $dueRent);

            $securityDeposit = $_POST['security_deposit'];
            $securityDeposit = str_replace($removeValues, '', $securityDeposit);

            $monthlyRent = $_POST['monthly_rent'];
            $monthlyRent = str_replace($removeValues, '', $monthlyRent);

            $unjustDamages = $_POST['unjust_damages'];
            $unjustDamages = str_replace($removeValues, '', $unjustDamages);

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

            $isIsResidential = false;
            $isNoQuitNotice = false;
            $isUnsatisfiedLease = false;
            $isBreachedConditionsLease = false;
            $isAmtGreaterThanZero = false;
            $isLeaseEnded = false;
            $isAdditionalRent = false;
            $isAbandoned = false;
            $isDeterminationRequest = false;

            //Lease Type
            $leaseType = $_POST['lease_type'];
            if ($leaseType == 'isResidential') {
                $isIsResidential = true;
            }

            //Notice Status
            $quitNotice = $_POST['quit_notice'];
            if ($quitNotice == 'no_quit_notice') {
                $isNoQuitNotice = true;
            }

            //Lease Status
            if (isset($_POST['unsatisfied_lease'])) {
                $isUnsatisfiedLease = true;
            }
            if (isset($_POST['breached_conditions_lease'])) {
                $isBreachedConditionsLease = true;
            }
            if (isset($_POST['breached_details'])) {
                $breachedDetails = $_POST['breached_details'];
            } else {
                $breachedDetails = '';
            }

            $propertyDamageDetails = $_POST['damages_details'];

            if (isset($_POST['term_lease_ended'])) {
                $isLeaseEnded = true;
            }

            if ($_POST['addit_rent'] == 'yes') {
                $isAdditionalRent = true;
            }

            if (isset($_POST['is_determination_request'])) {
                $isDeterminationRequest = true;
            }

            if (isset($_POST['is_abandoned'])) {
                $isAbandoned = true;
            }


            $defendantState = $_POST['state'];
            $defendantZipcode = $_POST['zipcode'];
            $defendanthouseNum = $_POST['houseNum'];
            $defendantStreetName = $_POST['streetName'];
            $defendantTown = $_POST['town'];

            if (is_numeric($_POST['additional_rent_amt'])) {
                $totalFees = (float)$additionalRentAmt + (float)$attorneyFees + (float)$dueRent + (float)$unjustDamages + (float)$damageAmt;
            } else {
                $totalFees = (float)$attorneyFees + (float)$dueRent + (float)$unjustDamages + (float)$damageAmt;
            }

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

            if ($noCommaTotalFees > 0) {
                $isAmtGreaterThanZero = true;
            }

            try {
                $eviction = new Evictions();
                $eviction->status = 'Created LTC';
                $eviction->total_judgement = $totalFees;
                $eviction->property_address = $defendanthouseNum.' '.$defendantStreetName.'-1'.$defendantTown .',' . $defendantState.' '.$defendantZipcode;
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
                $eviction->property_damage_details = $propertyDamageDetails;
                $eviction->is_residential = $isIsResidential;
                $eviction->no_quit_notice = $isNoQuitNotice;
                $eviction->unsatisfied_lease = $isUnsatisfiedLease;
                $eviction->breached_conditions_lease = $isBreachedConditionsLease;
                $eviction->amt_greater_than_zero = $isAmtGreaterThanZero;
                $eviction->lease_ended = $isLeaseEnded;
                $eviction->is_additional_rent = $isAdditionalRent;
                $eviction->defendant_state = $defendantState;
                $eviction->defendant_zipcode = $defendantZipcode;
                $eviction->defendant_house_num = $defendanthouseNum;
                $eviction->defendant_street_name = $defendantStreetName;
                $eviction->defendant_town = $defendantTown;
                $eviction->filing_fee = $filingFee;
                $eviction->is_abandoned = $isAbandoned;
                $eviction->is_determination_request = $isDeterminationRequest;
                $eviction->unit_num = $_POST['unit_number'];
                $eviction->additional_rent_amt = $_POST['additional_rent_amt'];
                $eviction->plantiff_name = $plantiffName;
                $eviction->plantiff_phone = $plantiffPhone;
                $eviction->plantiff_address_line_1 = $plantiffAddress1;
                $eviction->plantiff_address_line_2 = $plantiffAddress2;
                $eviction->verify_name = $verifyName;
                $eviction->user_id = Auth::user()->id;
                $eviction->file_type = 'eviction';

                $eviction->save();

                $evictionId = $eviction->id;

                $signature = new Signature();
                $signature->eviction_id = $evictionId;
                $signature->signature = $_POST['signature_source'];

                $signature->save();

                try {
                    Stripe::setApiKey('sk_test_MnFhi1rY4EF5NDsAWyURCRND');

                    $token = $_POST['stripeToken'];
                    \Stripe\Charge::create([
                        'amount' => 100,
                        'currency' => 'usd',
                        'description' => 'Eviction charge',
                        'source' => $token,
                    ]);
                } catch ( Exception $e ) {
                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

                    $errorMsg->save();
                    $mailer->sendMail('andrew.gaidis@gmail.com', 'LTC Error', $e->getMessage() );
                }

                try {
                    $notify = new NotificationController($courtNumber, Auth::user()->email);
                    $notify->notifyAdmin();
                    $notify->notifyJudge();
                    $notify->notifyMaker();
                } catch ( Exception $e) {
                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

                    $errorMsg->save();
                    $mailer->sendMail('andrew.gaidis@gmail.com', 'Notification Error' . Auth::user()->id, $e->getMessage());
                }


                return redirect('dashboard')->with('status','Your Eviction has been successfully made! You can see its progress in the table below.');
            } catch ( \Exception $e) {
                $errorMsg = new ErrorLog();
                $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

                $errorMsg->save();
                $mailer->sendMail('andrew.gaidis@gmail.com', 'LTC Error 1' . Auth::user()->id, '<html><body>
<table><thead>
<tr>
<th>Name</th>
<th>Data</th>
<th>Error Message</th>
</tr>
</thead>
<tbody>
<tr><td>Status</td><td>Created LTC</td><td>'.$e->getMessage().'</td></tr>
<tr><td>Property Address</td><td>'.$defendanthouseNum.' '.$defendantStreetName.'<br>'.$defendantTown .', ' . $defendantState.' '.$defendantZipcode.'</td></tr>
<tr><td>Tenant Name</td><td>'.$_POST['tenant_name'].'</td></tr>
<tr><td>Total Judgment</td><td>'. $_POST['total_judgment'] .'</td></tr>
<tr><td>Court Number</td><td>'.$courtNumber.'</td></tr>
<tr><td>Court Address</td><td>'.$courtAddressLine1.'<br>'.$courtAddressLine2.'</td></tr>
<tr><td>Owner Name</td><td>'.$ownerName.'</td></tr>
<tr><td>Magistrate Id</td><td>'.$magistrateId.'</td></tr>
<tr><td>Plantiff Name</td><td>'.$plantiffName.'</td></tr>
<tr><td>Plantiff Phone</td><td>'.$plantiffPhone.'</td></tr>
<tr><td>Plantiff Address</td><td>'.$plantiffAddress1.'<br>'.$plantiffAddress2.'</td></tr>
<tr><td>Verified Name</td><td><'.$verifyName.'/td></tr>
<tr><td>User Name</td><td>'.Auth::user()->name.'</td></tr>
<tr><td>Claim Description</td><td>'.$_POST['claim_description'].'</td></tr>
</tbody>
</table></body></html>' );
                alert('It looks like there was an issue while making this LTC. the Development team has been notified and are aware that your having issues. They will update you as soon as possible.');
            }
        } catch ( \Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            $mailer->sendMail('andrew.gaidis@gmail.com', 'LTC Error 2' . Auth::user()->id, $e->getMessage() );
            alert('It looks like there was an issue while making this LTC. the Development team has been notified and are aware that your having issues. They will update you as soon as possible.');
        }
    }

    public function getDigitalSignature() {
        $mailer = new Mailer();
        try {
            $courtNumber = explode('_', $_POST['courtNumber']);
            $isDigitalSignature = CourtDetails::where('magistrate_id', $courtNumber[1])->get();
            return $isDigitalSignature;
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            $mailer->sendMail('andrew.gaidis@gmail.com', 'LTC Error 3' . Auth::user()->id, $e->getMessage() );
            alert('It looks like there was an issue while making this LTC. the Development team has been notified and are aware that your having issues. They will update you as soon as possible.');
        }
    }
}