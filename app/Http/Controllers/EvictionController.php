<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\GeoLocation;
use GMaps;
use App\CourtDetails;
use JavaScript;
use App\Evictions;
use App\Signature;
use App\Classes\Mailer;
use Illuminate\Support\Facades\Log;


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

            $geoData = GeoLocation::orderBy('magistrate_id', 'ASC')->get();

            foreach ($geoData as $geo) {
                $township = CourtDetails::where('magistrate_id', $geo['magistrate_id'])->value('township');
                $geo['township'] = $township;
            }

            JavaScript::put([
                'geoData' => $geoData,
                'userId' => Auth::user()->role
            ]);

            return view('eviction', compact('map'));
        }
    }

    public function delete() {
        Log::info('Deleting an Eviction');
        Log::info(Auth::User()->id);
        try {
            $dbId = Evictions::where('id', $_POST['id'])->value('id');
            Evictions::destroy($dbId);
            return $dbId;
        } catch (\Exception $e) {
            return 'failed';
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

                return redirect('dashboard');
            } catch ( \Exception $e) {
                $mailer->sendMail('andrew.gaidis@gmail.com', 'LTC Error', '' );
                alert('It looks like there was an issue while making this LTC. the Development team has been notified and are aware that your having issues. They will update you as soon as possible.');
            }
        } catch ( \Exception $e) {
            $mailer->sendMail('andrew.gaidis@gmail.com', 'LTC Error', '' );
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
            $mailer->sendMail('andrew.gaidis@gmail.com', 'LTC Error', '' );
            alert('It looks like there was an issue while making this LTC. the Development team has been notified and are aware that your having issues. They will update you as soon as possible.');
        }
    }
}