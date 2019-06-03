<?php

namespace App\Http\Controllers;

use App\Classes\Mailer;
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
       $mailer = new Mailer();
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

            $mailer->sendMail('andrew.gaidis@gmail.com', 'Civil Complaint Error', '
<html><body>
<table><thead>
<tr>
<th>Name</th>
<th>Data</th>
<th>Error Message</th>
</tr>
</thead>
<tbody>
<tr><td>Status</td><td>Created Civil Complaint</td><td>'.$e->getMessage().'</td></tr>
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

                $signature->save();

                return redirect('dashboard');

            } catch ( Exception $e ) {
                $mailer->sendMail('andrew.gaidis@gmail.com', 'Civil Complaint Error', '
<html><body>
<table><thead>
<tr>
<th>Name</th>
<th>Data</th>
<th>Error Message</th>
</tr>
</thead>
<tbody>
<tr><td>Status</td><td>Created Civil Complaint</td><td>'.$e->getMessage().'</td></tr>
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

    }
}
