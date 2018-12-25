<?php

namespace App\Http\Controllers;

use App\GeoLocation;
use Dompdf\Options;
use GMaps;
use Dompdf\Dompdf;
use App\CourtDetails;
use JavaScript;
use App\Evictions;
use Illuminate\Support\Facades\Storage;
use SpacesConnect;


class EvictionController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $courtDetails = CourtDetails::all();
        $geoData = GeoLocation::orderBy('magistrate_id', 'ASC')->get();

        JavaScript::put([
            'magistrate' => $courtDetails,
            'geoData' => $geoData
        ]);

        return view('eviction', compact('map'));
    }

    public function delete() {
        try {
            $dbId = Evictions::where('id', $_POST['id'])->value('id');
            Evictions::destroy($dbId);
            return $dbId;
        } catch (\Exception $e) {
            return 'failed';
        }
    }

    public function formulatePDF() {

        try {
            $removeValues = ['$', ','];
            $magistrateId = str_replace('magistrate_' , '', $_POST['court_number']);
            $courtDetails = CourtDetails::where('magistrate_id', $magistrateId)->first();
            $geoDetails = GeoLocation::where('magistrate_id', $magistrateId)->first();

            $courtNumber = $courtDetails->court_number;

            $courtAddressLine1 = $geoDetails->address_line_one;
            $courtAddressLine2 = $geoDetails->address_line_two;

            //Attorney Fees
            $attorneyFees = $_POST['attorney_fees'];
            $attorneyFees = str_replace($removeValues, '', $attorneyFees);

            if ($_POST['attorney_fees'] != '') {
                $attorneyFeesCheckbox = '<input type="checkbox" checked/>';
            } else {
                $attorneyFeesCheckbox = '<input type="checkbox"/>';
            }


            $damageAmt = $_POST['damage_amt'];
            $damageAmt = str_replace($removeValues, '', $damageAmt);

            if ($damageAmt != '') {
                $damageAmtCheckbox = '<input type="checkbox" checked/>';
            } else {
                $damageAmtCheckbox = '<input type="checkbox"/>';
            }

            $dueRent = $_POST['due_rent'];
            $dueRent = str_replace($removeValues, '', $dueRent);

            $securityDeposit = $_POST['security_deposit'];
            $securityDeposit = str_replace($removeValues, '', $securityDeposit);

            $monthlyRent = $_POST['monthly_rent'];
            $monthlyRent = str_replace($removeValues, '', $monthlyRent);

            $unjustDamages = $_POST['unjust_damages'];
            $unjustDamages = str_replace($removeValues, '', $unjustDamages);


            $filing_date = $_POST['filing_date'];

            $tenantName = $_POST['tenant_name'];
            $ownerName = $_POST['owner_name'];

            $pmName = $_POST['pm_name'];
            $pmPhone = $_POST['pm_phone'];


            if ($_POST['rented_by_val'] == 'rentedByOwner') {
                $plaintiffLine = $ownerName;
                $ownerPMName = $ownerName;
            } else if ($_POST['rented_by_val'] == 'rentedByOther') {
                $ownerPMName = $pmName;
                $plaintiffLine = $_POST['other_name'] . ' on behalf of ' . $ownerName;
            } else {
                $ownerPMName = $ownerName;
                $plaintiffLine = $ownerName;
            }

            $additionalTenantAmt = 1;
            $additionalTenantFee = 0;

            if ($_POST['tenant_num'] == "2") {
                $upTo2000 = $courtDetails->two_defendant_up_to_2000;
                $btn20014000 = $courtDetails->two_defendant_between_2001_4000;
                $greaterThan4000 = $courtDetails->two_defendant_greater_than_4000;
                $oop = $courtDetails->two_defendant_out_of_pocket;
            } else if ($_POST['tenant_num'] == "1") {
                $upTo2000 = $courtDetails->one_defendant_up_to_2000;
                $btn20014000 = $courtDetails->one_defendant_between_2001_4000;
                $greaterThan4000 = $courtDetails->one_defendant_greater_than_4000;
                $oop = $courtDetails->one_defendant_out_of_pocket;
            } else {
                $upTo2000 = $courtDetails->three_defendant_up_to_2000;
                $btn20014000 = $courtDetails->three_defendant_between_2001_4000;
                $greaterThan4000 = $courtDetails->three_defendant_greater_than_4000;
                $oop = $courtDetails->three_defendant_out_of_pocket;
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

            //Lease Type
            $leaseType = $_POST['lease_type'];
            if ($leaseType == 'isResidential') {
                $isIsResidential = true;
                $isResidential = '<input type="checkbox" checked/>';
                $isNotResidential = '<input type="checkbox"/>';
            } else {
                $isResidential = '<input type="checkbox"/>';
                $isNotResidential = '<input type="checkbox" checked/>';
            }

            //Notice Status
            $quitNotice = $_POST['quit_notice'];
            if ($quitNotice == 'no_quit_notice') {
                $isNoQuitNotice = true;
                $noQuitNotice = '<input type="checkbox" checked/>';
                $quitNoticeGiven = '<input type="checkbox"/>';
            } else {
                $noQuitNotice = '<input type="checkbox"/>';
                $quitNoticeGiven = '<input type="checkbox" checked/>';
            }

            //Lease Status
            if (isset($_POST['unsatisfied_lease'])) {
                $isUnsatisfiedLease = true;
                $unsatisfiedLease = '<input type="checkbox" checked/>';
            } else {
                $unsatisfiedLease = '<input type="checkbox"/>';
            }
            if (isset($_POST['breached_conditions_lease'])) {
                $isBreachedConditionsLease = true;
                $breachedConditionsLease = '<input type="checkbox" checked/>';
            } else {
                $breachedConditionsLease = '<input type="checkbox"/>';
            }
            $breachedDetails = $_POST['breached_details'];
            $propertyDamageDetails = $_POST['damages_details'];

            if (isset($_POST['term_lease_ended'])) {
                $isLeaseEnded = true;
                $leaseEnded = '<input type="checkbox" checked/>';
            } else {
                $leaseEnded = '<input type="checkbox"/>';
            }

            if ($_POST['addit_rent'] == 'yes') {
                $additionalRent = '<input type="checkbox" checked/>';
            } else {
                $additionalRent = '<input type="checkbox"/>';
            }

            if ($_POST['unjust_damages'] != '') {
                $unjustDamagesCheckbox = '<input type="checkbox" checked/>';
            } else {
                $unjustDamagesCheckbox = '<input type="checkbox"/>';
            }

            if (isset($_POST['is_determination_request'])) {
                $isDeterminationRequest = true;
                $determinationRequestCheckbox = '<input type="checkbox" checked/>';
            } else {
                $isDeterminationRequest = false;
                $determinationRequestCheckbox = '<input type="checkbox"/>';
            }

            if (isset($_POST['is_abandoned'])) {
                $isAbandoned = true;
                $abandonedCheckbox = '<input type="checkbox" checked/>';
            } else {
                $isAbandoned = false;
                $abandonedCheckbox = '<input type="checkbox"/>';
            }


            $defendantState = $_POST['state'];
            $defendantZipcode = $_POST['zipcode'];
            $defendanthouseNum = $_POST['houseNum'];
            $defendantStreetName = $_POST['streetName'];
            $defendantTown = $_POST['town'];



            $totalFees = (float)$attorneyFees + (float)$dueRent + (float)$unjustDamages + (float)$damageAmt;

            $totalFees = number_format($totalFees, 2);

            if ($totalFees < 2000) {
                $filingFee = $upTo2000 + $additionalTenantFee;
            } else if ($totalFees >= 2000 && $totalFees <= 4000) {
                $filingFee = $btn20014000 + $additionalTenantFee;
            } else if ($totalFees > 4000) {
                $filingFee = $greaterThan4000 + $additionalTenantFee;
            } else {
                $filingFee = 'Didnt Work';
            }

            mail('andrew.gaidis@gmail.com', 'filing fee', $filingFee);

            if ($totalFees > 0) {
                $isAmtGreaterThanZero = true;
                $amtGreaterThanZeroCheckbox = '<input type="checkbox" checked/>';
            } else {
                $amtGreaterThanZeroCheckbox = '<input type="checkbox"/>';
            }

            try {
                $eviction = new Evictions();
                $eviction->status = 'Created LTC';
                $eviction->total_judgement = $totalFees;
                $eviction->property_address = $defendanthouseNum.' '.$defendantStreetName.'-1'.$defendantTown .',' . $defendantState.' '.$defendantZipcode;
                $eviction->owner_name = $ownerName;
                $eviction->tenant_name = $tenantName;
                $eviction->court_filing_fee = $filingFee;
                $eviction->pdf_download = 'true';
                $eviction->court_number = $courtNumber;
                $eviction->court_address_line_1 = $courtAddressLine1;
                $eviction->court_address_line_2 = $courtAddressLine2;
                $eviction->magistrate_id = $magistrateId;
                $eviction->attorney_fees = $attorneyFees;
                $eviction->damage_amt = $damageAmt;
                $eviction->due_rent = $dueRent;
                $eviction->security_deposit = $securityDeposit;
                $eviction->monthly_rent = $monthlyRent;
                $eviction->unjust_damages = $unjustDamages;
                $eviction->breached_details = $breachedDetails;
                $eviction->property_damage_details = $propertyDamageDetails;
                $eviction->plaintiff_line = $plaintiffLine;
                $eviction->is_residential = $isIsResidential;
                $eviction->no_quit_notice = $isNoQuitNotice;
                $eviction->unsatisfied_lease = $isUnsatisfiedLease;
                $eviction->breached_conditions_lease = $isBreachedConditionsLease;
                $eviction->amt_greater_than_zero = $isAmtGreaterThanZero;
                $eviction->lease_ended = $isLeaseEnded;
                $eviction->defendant_state = $defendantState;
                $eviction->defendant_zipcode = $defendantZipcode;
                $eviction->defendant_house_num = $defendanthouseNum;
                $eviction->defendant_street_name = $defendantStreetName;
                $eviction->defendant_town = $defendantTown;
                $eviction->filing_fee = $filingFee;
                $eviction->pm_name = $pmName;
                $eviction->pm_phone = $pmPhone;
                $eviction->is_abandoned = $isAbandoned;
                $eviction->is_determination_request = $isDeterminationRequest;
                $eviction->unit_num = $_POST['unit_number'];

                $eviction->save();

            } catch ( \Exception $e) {
                mail('andrew.gaidis@gmail.com', 'formulatePDF Error', $e->getMessage());
                return back();
            }

            $dompdf = new Dompdf();
            $options = new Options();
            $options->setIsRemoteEnabled(true);
            $dompdf->setOptions($options);
            $dompdf->loadHtml('<html>
<head><meta http-equiv=Content-Type content="text/html; charset=UTF-8">
<style type="text/css">
<!--
input[type=checkbox] { display: inline!important; font-size: 9pt; margin:1%; }
span.cls_003{font-family:Arial,serif;font-size:13.30px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_003{font-family:Arial,serif;font-size:13.30px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_002{font-family:Arial,serif;font-size:18.75px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_002{font-family:Arial,serif;font-size:18.75px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_005{font-family:Arial,serif;font-size:9.31px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_005{font-family:Arial,serif;font-size:9.31px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_004{font-family:Arial,serif;font-size:12.10px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_004{font-family:Arial,serif;font-size:12.10px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_006{font-family:Arial,serif;font-size:7.98px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_006{font-family:Arial,serif;font-size:7.98px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_009{font-family:Arial,serif;font-size:9.31px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: underline}
span.cls_009{font-family:Arial,serif;font-size:9.31px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_007{font-family:Arial,serif;font-size:10.77px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_007{font-family:Arial,serif;font-size:10.77px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_008{font-family:Arial,serif;font-size:10.77px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_008{font-family:Arial,serif;font-size:10.77px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_010{font-family:Arial,serif;font-size:10.77px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: underline}
span.cls_010{font-family:Arial,serif;font-size:10.77px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
--></style></head><body>
<span style="position:absolute;margin-left:-44px;top:-22px;width:787px;height:1112px;overflow:hidden">
<span style="position:absolute;left:0px;top:0px"><img src="https://quickevict.nyc3.digitaloceanspaces.com/background1.jpg" width="800" height="1052"></span>
<span style="position:absolute;left:47.95px;top:16.85px" class="cls_003"><span class="cls_003">COMMONWEALTH OF PENNSYLVANIA</span></span><br>
<span style="position:absolute;left:460.45px;top:16.80px" class="cls_002"><span class="cls_002">LANDLORD/TENANT COMPLAINT</span></span><br>
<span style="position:absolute;left:47.95px;top:29.55px" class="cls_003"><span class="cls_003">COUNTY OF ' . strtoupper($courtDetails->county) .'</span></span><br>
<span style="position:absolute;left:447.28px;top:86.80px" class="cls_005"><span class="cls_005">PLAINTIFF:</span><br><p style="margin-left:6px;">'. $plaintiffLine .'<br>PO Box 5304<br>Lancaster, PA 17606<br>'.$pmPhone.'</p></span><br>
<span style="position:absolute;left:600.50px;top:86.80px" class="cls_005"><span class="cls_005">NAME and ADDRESS</span></span>
<span style="position:absolute;left:55.40px;top:92.36px" class="cls_004"><span class="cls_004">Mag. Dist. No: '. $courtNumber .'</span></span><br>
<span style="position:absolute;left:55.40px;top:105.85px" class="cls_004"><span class="cls_004">MDJ Name: '. $courtDetails->mdj_name .'</span></span><br>
<span style="position:absolute;left:55.40px;top:120.05px" class="cls_004"><span class="cls_004">Address: '.$courtAddressLine1.'<br><span style="margin-left:45px;">'.$courtAddressLine2.'</span></span></span><br>
<span style="position:absolute;left:581.34px;top:183.90px" class="cls_006"><span class="cls_006">V.</span></span><br>
<span style="position:absolute;left:447.28px;top:180.90px" class="cls_009"><span class="cls_009">DEFENDANT:</span><br><p style="margin-left:6px;">'.$tenantName.'<br>'.$defendanthouseNum.' '.$defendantStreetName.' '. $_POST['unit_number'] . '<br>'.$defendantTown .',' . $defendantState.' '.$defendantZipcode.'  </p></span><br>
<span style="position:absolute;left:600.50px;top:180.00px" class="cls_005"><span class="cls_005">NAME and ADDRESS</span></span><br>
<span style="position:absolute;left:55.40px;top:188.45px" class="cls_004"><span class="cls_004">Telephone: '.$courtDetails->phone_number.'</span></span><br>
<span style="position:absolute;left:195.45px;top:214.95px" class="cls_004"><span class="cls_004">AMOUNT</span></span><br>
<span style="position:absolute;left:293.35px;top:214.95px" class="cls_004"><span class="cls_004">DATE PAID</span></span><br>
<span style="position:absolute;left:55.40px;top:234.95px" class="cls_004"><span class="cls_004">FILING COSTS:</span></span><br>
<span style="position:absolute;left:152.00px;top:234.95px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:55.40px;top:252.95px" class="cls_004"><span class="cls_004">POSTAGE</span></span><br>
<span style="position:absolute;left:152.00px;top:252.95px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:480.55px;top:262.45px" class="cls_004"><span class="cls_004">Docket No: </span></span><br>
<span style="position:absolute;left:55.40px;top:270.95px" class="cls_004"><span class="cls_004">SERVICE COSTS</span></span><br>
<span style="position:absolute;left:152.00px;top:270.95px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:480.55px;top:272.95px" class="cls_004"><span class="cls_004">Case Filed:</span></span><br>
<span style="position:absolute;left:55.40px;top:288.95px" class="cls_004"><span class="cls_004">CONSTABLE ED.</span></span><br>
<span style="position:absolute;left:152.00px;top:288.95px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:55.40px;top:313.95px" class="cls_004"><span class="cls_004">TOTAL</span></span><br>
<span style="position:absolute;left:152.00px;top:313.95px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:55.40px;top:335.95px" class="cls_003"><span class="cls_003">Pa.R.C.P.M.D.J. No. 206 sets forth those costs recoverable by the prevailing party.</span></span><br>
<span style="position:absolute;left:60.40px;top:355.95px" class="cls_004"><span class="cls_004">TO THE DEFENDANT: The above named plaintiff(s) asks judgment together with costs against you for the possession of real</span></span><br>
<span style="position:absolute;left:82.77px;top:365.51px" class="cls_004"><span class="cls_004">property and for:</span></span><br>
<span style="position:absolute;left:90.87px;top:385.21px" class="cls_004"><span class="cls_004">Lease is</span><span style="margin-left:350px;">'.$monthlyRent.'</span></span><br>
<span style="position:absolute;left:165.25px;top:385.21px" class="cls_004"><span class="cls_004">'. $isResidential .'Residential</span></span><br>
<span style="position:absolute;left:260.23px;top:385.21px" class="cls_004"><span class="cls_004">'. $isNotResidential .'Nonresidential     Monthly Rent  $</span></span><br>
<span style="position:absolute;left:550.98px;top:385.21px" class="cls_004"><span class="cls_004">Security Deposit $</span><span style="margin-left:30px;">'.$securityDeposit.'</span></span><br>
<span style="position:absolute;left:55.40px;top:404.91px" class="cls_004"><span class="cls_004">'. $abandonedCheckbox . ' A determination that the manufactured home and property have been abandoned.</span></span><br>
<span style="position:absolute;left:55.40px;top:420.61px" class="cls_004"><span class="cls_004">'. $determinationRequestCheckbox . ' A Request for Determination of Abandonment (Form MDJS 334) must be completed and submitted with this complaint.</span></span><br>
<span style="position:absolute;left:55.40px;top:435.71px" class="cls_004"><span class="cls_004">'. $damageAmtCheckbox . ' Damages for injury to the real property, to wit: ___<span style="text-decoration:underline;">'.$propertyDamageDetails.'</span></span></span><br>
<span style="position:absolute;left:55.40px;top:450.21px" class="cls_004"><span class="cls_004">______________________________________________________________  in the amount of:</span></span><br>
<span style="position:absolute;left:600.40px;top:464.45px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.40px;top:464.21px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________'.$damageAmt.'_________</span></span><br>
<span style="position:absolute;left:60.50px;top:484.95px" class="cls_004"><span class="cls_004">'. $unjustDamagesCheckbox . 'Damages for the unjust detention of the real property in the amount of</span></span><br>
<span style="position:absolute;left:600.40px;top:484.95px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.42px;top:504.95px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________'.$unjustDamages.'_________</span></span><br>
<span style="position:absolute;left:60.50px;top:504.45px" class="cls_004"><span class="cls_004">'. $amtGreaterThanZeroCheckbox .' Rent remaining due and unpaid on filing date in the amount of</span></span><br>
<span style="position:absolute;left:600.40px;top:524.45px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.40px;top:524.45px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________'.$dueRent.'_________</span></span><br>
<span style="position:absolute;left:60.50px;top:544.95px" class="cls_004"><span class="cls_004">'. $additionalRent .' And additional rent remaining due and unpaid on hearing date</span></span><br>
<span style="position:absolute;left:600.40px;top:544.95px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.40px;top:564.95px" class="cls_004"><span class="cls_004">___________________</span></span><br>
<span style="position:absolute;left:60.50px;top:564.45px" class="cls_004"><span class="cls_004">' . $attorneyFeesCheckbox . ' Attorney fees in the amount of</span></span><br>
<span style="position:absolute;left:600.40px;top:584.45px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.40px;top:584.45px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________'.$attorneyFees.'_________</span></span><br>
<span style="position:absolute;left:42.30px;top:607.20px" class="cls_004"><span class="cls_004">THE PLAINTIFF FURTHER ALLEGES THAT:</span></span><br>
<span style="position:absolute;left:580.40px;top:607.20px" class="cls_004"><span class="cls_004">Total:</span></span><br>
<span style="position:absolute;left:600.40px;top:612.20px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.40px;top:615.20px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________'.$totalFees.'_________</span></span><br>
<span style="position:absolute;left:55.40px;top:630.15px" class="cls_004"><span class="cls_004">1. The location and the address, if any, of the real property is:</span></span><br>
<span style="position:absolute;left:393.85px;top:630.15px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">'.$defendanthouseNum.' '.$defendantStreetName . ', ' .$_POST['unit_number'].', ' . $defendantTown .','.$defendantState.' '.$defendantZipcode . '</span></span><br>
<span style="position:absolute;left:55.40px;top:640.05px" class="cls_004"><span class="cls_004">2. The plaintiff is the landlord of that property.</span></span><br>
<span style="position:absolute;left:55.40px;top:650.55px" class="cls_004"><span class="cls_004">3. The plaintiff leased or rented the property to you or to ___________________________________________under whom you claim</span></span><br>
<span style="position:absolute;left:55.40px;top:660.65px" class="cls_004"><span class="cls_004">4.</span></span><br>
<span style="position:absolute;left:65.60px;top:670.65px" class="cls_004"><span class="cls_004">'.$quitNoticeGiven.'Notice to quit was given in accordance with law, or</span></span><br>
<span style="position:absolute;left:65.60px;top:680.15px" class="cls_004"><span class="cls_004">'.$noQuitNotice.'No notice is required under the terms of the lease.</span></span><br>
<span style="position:absolute;left:55.40px;top:690.45px" class="cls_004"><span class="cls_004">5.</span></span><br>
<span style="position:absolute;left:77.30px;top:700.45px" class="cls_004"><span class="cls_004">'.$leaseEnded.'The term for which the property was leased or rented is fully ended, or</span></span><br>
<span style="position:absolute;left:77.30px;top:710.35px" class="cls_004"><span class="cls_004">'.$breachedConditionsLease.'A forfeiture has resulted by reason of a breach of the conditions of the lease, to wit:</span></span><br>
<span style="position:absolute;left:414.74px;top:720.35px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">'.$breachedDetails.'_____</span></span>
<span style="position:absolute;left:77.30px;top:730.35px" class="cls_004"><span class="cls_004">________________________________________________________________________________________________or,</span></span><br>
<span style="position:absolute;left:77.30px;top:740.15px" class="cls_004"><span class="cls_004">___________________________________________________________________________________________________</span></span><br>
<span style="position:absolute;left:77.30px;top:750.55px" class="cls_004"><span class="cls_004">'.$unsatisfiedLease.'Rent reserved and due has, upon demand, remained unsatisfied.</span></span><br>
<span style="position:absolute;left:42.30px;top:760.15px" class="cls_004"><span class="cls_004">6.</span></span><br>
<span style="position:absolute;left:60.50px;top:760.15px" class="cls_004"><span class="cls_004">You retain the real property and refuse to give up to its possession.</span></span><br>
<span style="position:absolute;left:42.00px;top:770.65px" class="cls_004"><span class="cls_004">I, <span style="text-decoration:underline;"> ' . $ownerPMName . ' </span> verify that the facts set forth in this complaint are</span></span><br>
<span style="position:absolute;left:42.00px;top:780.85px" class="cls_004"><span class="cls_004">true and correct to the best of my knowledge, information and belief. This statement is made subject to the penalties of Section 4904</span></span><br>
<span style="position:absolute;left:42.00px;top:790.05px" class="cls_004"><span class="cls_004">of the Crimes Code (18 PA. C.S. § 4904) relating to unsworn falsification to authorities.</span></span><br>
<span style="position:absolute;left:42.30px;top:800.90px" class="cls_004"><span class="cls_004">I certify this filing complies with the UJS Case Records Public Access Policy.</span></span><br>
<span style="position:absolute;left:560.00px;top:876.80px" class="cls_004"><span class="cls_004">(Signature of Plaintiff)</span></span><br>
<span style="position:absolute;left:60.00px;top:900.40px" class="cls_004"><span class="cls_004">The plaintiff\'s attorney shall file an entry of appearance with the magisterial district court pursuant to Pa . R . C . P . M . D . J . 207.1 </span ></span ><br >
<span style = "position:absolute;left:60.90px;top:915.15px" class="cls_005" ><span class="cls_005" >IF YOU HAVE A DEFENSE to this complaint you may present it at the hearing . IF YOU HAVE A CLAIM against the plaintiff arising out of the occupancy of the premises,</span ></span ><br >
<span style = "position:absolute;left:60.90px;top:925.30px" class="cls_005" ><span class="cls_005" > which is in the magisterial district judge jurisdiction and which you intend to assert at the hearing, YOU MUST FILE it on the complaint form at the office BEFORE THE TIME </span ></span ><br >
<span style = "position:absolute;left:60.90px;top:935.45px" class="cls_005" ><span class="cls_005" > set for the hearing . IF YOU DO NOT APPEAR AT THE HEARING, a judgment for possession and costs, and for damages and rent if claimed, may nevertheless be entered </span ></span ><br >
<span style = "position:absolute;left:60.90px;top:945.60px" class="cls_005" ><span class="cls_005" > against you . A judgment against you for possession may result in your EVICTION from the premises .</span ></span ><br >
<span style = "position:absolute;left:60.90px;top:955.75px" class="cls_007" ><span class="cls_007" >If you are disabled and require a reasonable accommodation to gain access to the Magisterial District Court and its services, please </span ></span ><br >
<span style = "position:absolute;left:60.90px;top:965.35px" class="cls_007" ><span class="cls_007" > contact the Magisterial District Court at the above address or telephone number . We are unable to provide transportation .</span ></span ><br >
<span style = "position:absolute;left:55.40px;top:985.85px" class="cls_008" ><span class="cls_008" > AOPC 310A </span ></span ><br >
<span style = "position:absolute;left:303.75px;top:985.50px" class="cls_008" ><span class="cls_008" > 1</span ></span ><br >
<span style = "position:absolute;left:471.65px;top:1050px" class="cls_007" ><span class="cls_007" > </span >Filing Fee: $'.$filingFee.'</span ><br >
<span style = "position:absolute;left:452.45px;top:1050px" class="cls_010" ><span class="cls_010" > </span ></span >
</span ></body ></html>');

            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->stream();


            return view('eviction', compact('map'));
        } catch ( \Exception $e) {
            mail('andrew.gaidis@gmail.com', 'formulatePDF Error', $e->getMessage());
            return back();

        }
    }


    public function saveSignature() {

        try {
            $key = "EH2TKHIPLFYD7LHGDFL4";
            $secret = "MXWANmAm4UPHVY0++0C1bmbfz9DE2jSD+BBOqNEL8kU";

            $space_name = "quickevict";
            $region = "nyc3";

            $space = new SpacesConnect($key, $secret, $space_name, $region);


            $imagedata = base64_decode($_POST['img_data']);
            $filename = md5(date("dmYhisA"));
            //Location to where you want to created sign image
            $file_name = './doc_signs/'.$filename.'.png';
           // file_put_contents($file_name,$imagedata);
            Storage::put('signature.png', $file_name);



        } catch ( \Exception $e ) {
            mail('andrew.gaidis@gmail.com', 'save Signature Error', $e->getMessage());
        }
    }


//    public function addFile(Request $request) {
//        try {
//            mail('andrew.gaidis@gmail.com', 'formulatePDF Success', $request);
//            $request->pdf->storeAs('pdf', $request->pdf->getClientOriginalName());
//            mail('andrew.gaidis@gmail.com', 'formulatePDF Success', $_POST['pdf']);
//            return $_POST;
//        } catch ( \Exception $e) {
//            mail('andrew.gaidis@gmail.com', 'adding File Error', $e->getMessage());
//        }
//    }
}