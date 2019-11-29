<?php

namespace App\Http\Controllers;

use App\Classes\Mailer;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\GeoLocation;
use GMaps;
use App\CourtDetails;
use App\Evictions;
use App\Signature;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use stdClass;
use App\PDF;
use Dompdf\Options;
use Dompdf\Dompdf;

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
        $magistrateId = str_replace('magistrate_' , '', $_POST['court_number']);
        $courtDetails = CourtDetails::where('magistrate_id', $magistrateId)->first();
        $geoDetails = GeoLocation::where('magistrate_id', $magistrateId)->first();
        $pdfHtml = PDF::where('name', 'oop')->first();
        $pdfEditor = new PDFEditController();
        $evictionData = new stdClass();

        $plaintiffName = $_POST['owner_name'];
        $plaintiffPhone = $_POST['owner_phone'];
        $plaintiffAddress1 = $_POST['owner_address_1'];
        $plaintiffAddress2 = $_POST['owner_address_2'];

        $plaintiffAddress = $plaintiffName .'<br>'. $plaintiffAddress1 .'<br>'. $plaintiffAddress2 .'<br>'. $plaintiffPhone;
        $defendantAddress = $_POST['tenant_name'] . '<br>' . $_POST['houseNum'] . ' ' . $_POST['streetName'] . ', ' . $_POST['unit_number'] .' '. $_POST['town'] .', '. $_POST['state'] .' '. $_POST['zipcode'];

        $evictionData->id = '-1';
        $evictionData->plantiff_name = $plaintiffName;
        $evictionData->court_address_line_1 = $geoDetails->address_line_one;
        $evictionData->court_address_line_2 = $geoDetails->address_line_two;
        $evictionData->claim_description = $_POST['claim_description'];

        $pdfEditor->globalHtmlAttributes($pdfHtml, $courtDetails, $plaintiffAddress, $defendantAddress, $_POST['signature_source'], $evictionData);
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
        $domPdf->stream();
    }

    public function formulatePDF()
    {
        $mailer = new Mailer();
        try {
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
            $civilDefendantAddress1 = $_POST['civil_defendant_address_1'];
            $civilDefendantAddress2 = $_POST['civil_defendant_address_2'];

            $eviction = new Evictions();
            $eviction->status = 'Created Civil Complaint';
            $eviction->property_address = $defendanthouseNum.' '.$defendantStreetName.'-1'.$defendantTown .', ' . $defendantState.' '.$defendantZipcode;
            $eviction->tenant_name = $_POST['tenant_name'];
            $eviction->defendant_state = $civilDefendantAddress1;
            $eviction->defendant_zipcode = $civilDefendantAddress2;
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

            try {
                Stripe::setApiKey('sk_test_MnFhi1rY4EF5NDsAWyURCRND');

                $token = $_POST['stripeToken'];
                \Stripe\Charge::create([
                    'amount' => 100,
                    'currency' => 'usd',
                    'description' => 'Civil Complaint charge',
                    'source' => $token,
                ]);
            } catch ( Exception $e ) {
                Log::info($e->getMessage());
                $mailer->sendMail('andrew.gaidis@gmail.com', 'OOP Error', $e->getMessage() );
            }

            $notify = new NotificationController($courtNumber, Auth::user()->email);
            $notify->notifyAdmin();
            $notify->notifyJudge();
            $notify->notifyMaker();

            return redirect('dashboard')->with('status','Your Civil Complaint has been successfully made! You can see its progress in the table below.');

        } catch ( Exception $e ) {
            $mailer->sendMail('andrew.gaidis@gmail.com, chad@slatehousegroup.com ', 'Civil Complaint Error', '
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
