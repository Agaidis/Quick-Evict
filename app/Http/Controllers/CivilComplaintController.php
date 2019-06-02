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

class CivilComplaintController extends Controller
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

            return view('civilComplaint', compact('map'));
        }
    }

    public function formulatePDF()
    {
        Log::info('formulatePDF OOP');
        Log::info(Auth::User()->id);
        try {
            $removeValues = ['$', ','];
            $magistrateId = str_replace('magistrate_' , '', $_POST['court_number']);
            $courtDetails = CourtDetails::where('magistrate_id', $magistrateId)->first();
            $geoDetails = GeoLocation::where('magistrate_id', $magistrateId)->first();

            $courtNumber = $courtDetails->court_number;

            $courtAddressLine1 = $geoDetails->address_line_one;
            $courtAddressLine2 = $geoDetails->address_line_two;


            $ownerName = $_POST['owner_name'];


                $verifyName = $_POST['owner_name'];
                $plantiffName = $_POST['owner_name'];
                $plantiffPhone = $_POST['owner_phone'];
                $plantiffAddress1 = $_POST['owner_address_1'];
                $plantiffAddress2 = $_POST['owner_address_2'];


            $defendantState = $_POST['state'];
            $defendantZipcode = $_POST['zipcode'];
            $defendanthouseNum = $_POST['houseNum'];
            $defendantStreetName = $_POST['streetName'];
            $defendantTown = $_POST['town'];

            try {
                $eviction = new Evictions();
                $eviction->status = 'Created Civil Complaint';
                $eviction->property_address = $defendanthouseNum.' '.$defendantStreetName.'-1'.$defendantTown .', ' . $defendantState.' '.$defendantZipcode;
                $eviction->tenant_name = $_POST['tenant_name'];
                $eviction->defendant_state = $defendantState;
                $eviction->defendant_zipcode = $defendantZipcode;
                $eviction->defendant_house_num = $defendanthouseNum;
                $eviction->defendant_street_name = $defendantStreetName;
                $eviction->defendant_town = $defendantTown;
                $eviction->total_judgement = $_POST['total_judgment'];
                $eviction->pdf_download = 'true';
                $eviction->court_number = $courtNumber;
                $eviction->court_address_line_1 = $courtAddressLine1;
                $eviction->court_address_line_2 = $courtAddressLine2;
                $eviction->owner_name = $ownerName;
                $eviction->magistrate_id = $magistrateId;
                $eviction->plantiff_name = $plantiffName;
                $eviction->plantiff_phone = $plantiffPhone;
                $eviction->plantiff_address_line_1 = $plantiffAddress1;
                $eviction->plantiff_address_line_2 = $plantiffAddress2;
                $eviction->verify_name = $verifyName;
                $eviction->user_id = Auth::user()->id;
                $eviction->court_filing_fee = '0';
                $eviction->claim_description = $_POST['claim_description'];
                $eviction->file_type = 'civil complaint';

                $eviction->save();

                $evictionId = $eviction->id;

                $signature = new Signature();
                $signature->eviction_id = $evictionId;
                $signature->signature = $_POST['signature_source'];

                $signature->save();

            } catch ( Exception $e ) {
                Log::info($e);
                mail('andrew.gaidis@gmail.com', 'formulatePDFData Error' . Auth::User()->id, $e->getMessage());
                print_r($e->getMessage());
            }
            return redirect('dashboard');
        } catch ( Exception $e ) {
            Log:info($e);
            mail('andrew.gaidis@gmail.com', 'formulatePDFCreation  Civil Complaint Error' . Auth::User()->id, $e->getMessage());
            print_r($e->getMessage());
        }
    }
}
