<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\GeoLocation;
use Dompdf\Options;
use GMaps;
use Dompdf\Dompdf;
use App\CourtDetails;
use JavaScript;
use App\Evictions;
use App\Signature;
use Illuminate\Support\Facades\Log;

class OrderOfPossessionController extends Controller
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

            return view('orderOfPossession', compact('map'));
        }
    }

    public function formulatePDF()
    {
        try {
            $magistrateId = str_replace('magistrate_' , '', $_POST['court_number']);
            $courtDetails = CourtDetails::where('magistrate_id', $magistrateId)->first();
            $geoDetails = GeoLocation::where('magistrate_id', $magistrateId)->first();

            $courtNumber = $courtDetails->court_number;

            $isAmtGreaterThanZero = false;

            $courtAddressLine1 = $geoDetails->address_line_one;
            $courtAddressLine2 = $geoDetails->address_line_two;

            $tenantName = implode(', ', $_POST['tenant_name']);

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

            $totalFees = (float)$_POST['judgment_amount'] + (float)$_POST['costs_original_lt_proceeding'] + (float)$_POST['costs_this_proceeding'] + (float)$_POST['attorney_fees'];

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
                $eviction->status = 'Created OOP';
                $eviction->total_judgement = $totalFees;
                $eviction->judgment_amount = $_POST['judgment_amount'];
                $eviction->costs_original_lt_proceeding = $_POST['costs_original_lt_proceeding'];
                $eviction->cost_this_proceeding = $_POST['costs_this_proceeding'];
                $eviction->attorney_fees = $_POST['attorney_fees'];
                $eviction->property_address = $defendanthouseNum.' '.$defendantStreetName.'-1'.$defendantTown .',' . $defendantState.' '.$defendantZipcode;
                $eviction->defendant_state = $defendantState;
                $eviction->defendant_zipcode = $defendantZipcode;
                $eviction->defendant_house_num = $defendanthouseNum;
                $eviction->defendant_street_name = $defendantStreetName;
                $eviction->defendant_town = $defendantTown;
                $eviction->tenant_name = $tenantName;
                $eviction->pdf_download = 'true';
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
                $eviction->verify_name = $verifyName;
                $eviction->unit_num = $_POST['unit_number'];
                $eviction->user_id = Auth::user()->id;
                $eviction->docket_number = $_POST['docket_number'];
                $eviction->date_of_oop = date("m/d/Y");
                $eviction->court_filing_fee = '0';
                $eviction->filing_fee = $filingFee;
                $eviction->file_type = 'oop';

                $eviction->save();

                $evictionId = $eviction->id;

                $signature = new Signature();
                $signature->eviction_id = $evictionId;
                $signature->signature = $_POST['signature_source'];

                $signature->save();

            } catch ( Exception $e ) {
                mail('andrew.gaidis@gmail.com', 'formulatePDFData Error' . Auth::User()->id, $e->getMessage());
                print_r($e->getMessage());
            }

            return redirect('dashboard');
        } catch ( Exception $e ) {
            mail('andrew.gaidis@gmail.com', 'formulatePDFCreation Error' . Auth::User()->id, $e->getMessage());
           print_r($e->getMessage());
        }
    }
}
