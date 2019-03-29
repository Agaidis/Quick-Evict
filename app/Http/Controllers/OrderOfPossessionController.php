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
                'geoData' => $geoData
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

            $courtAddressLine1 = $geoDetails->address_line_one;
            $courtAddressLine2 = $geoDetails->address_line_two;

            $pmName = $_POST['pm_name'];
            $ownerName = $_POST['owner_name'];

            if ($_POST['rented_by_val'] == 'rentedByOwner') {
                $verifyName = $_POST['owner_name'];
                $plantiffName = $_POST['owner_name'];
                $plantiffNameBreakpoint = $_POST['owner_name'];
                $plantiffPhone = $_POST['owner_phone'];
                $plantiffAddress1 = $_POST['owner_address_1'];
                $plantiffAddress2 = $_POST['owner_address_2'];
            } else {
                $verifyName = $pmName;
                $plantiffName = $_POST['other_name'] . ' on behalf of ' . $_POST['owner_name'];
                $plantiffNameBreakpoint = $_POST['other_name'] . ' on behalf of <br>' . $_POST['owner_name'];
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

            try {
                $eviction = new Evictions();
                $eviction->status = 'Created OOP';
                $eviction->judgment_amount = $_POST['judgment_amount'];
                $eviction->costs_original_lt_proceeding = $_POST['costs_original_lt_proceeding'];
                $eviction->cost_this_proceeding = $_POST['costs_this_proceeding'];
                $eviction->attorney_fees = $_POST['attorney_fees'];
                $eviction->total_judgement = '';
                $eviction->property_address = $defendanthouseNum.' '.$defendantStreetName.'-1'.$defendantTown .',' . $defendantState.' '.$defendantZipcode;
                $eviction->defendant_state = $defendantState;
                $eviction->defendant_zipcode = $defendantZipcode;
                $eviction->defendant_house_num = $defendanthouseNum;
                $eviction->defendant_street_name = $defendantStreetName;
                $eviction->defendant_town = $defendantTown;
                $eviction->tenant_name = $_POST['tenant_name'];
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
                $eviction->docket_number = $_POST['docket_number'];
                $eviction->date_of_oop = date("d/m/Y");
                $eviction->court_filing_fee = '0';
                $eviction->file_type = 'oop';

                $eviction->save();

                $evictionId = $eviction->id;

                $signature = new Signature();
                $signature->eviction_id = $evictionId;
                $signature->signature = $_POST['signature_source'];

                $signature->save();

            } catch ( \Exception $e) {
                mail('andrew.gaidis@gmail.com', 'formulatePDFData Error' . Auth::User()->id, $e->getMessage());
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
span.cls_002{font-family:Arial,serif;font-size:19px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_003{font-family:Arial,serif;font-size:13px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_004{font-family:Arial,serif;font-size:13px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_005{font-family:Arial,serif;font-size:13px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_006{font-family:Arial,serif;font-size:8px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_007{font-family:Arial,serif;font-size:11px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_008{font-family:Arial,serif;font-size:11px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_009{font-family:Arial,serif;font-size:9px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: underline}
--></style></head><body>
<span style="position:absolute;margin-left:-44px;top:-22px;width:787px;height:1112px;overflow:hidden">
<span style="position:absolute;left:0px;top:0px"><img src="https://quickevict.nyc3.digitaloceanspaces.com/oop.jpg" width="800" height="1052"></span>
<span style="position:absolute;left:48px;top:16px" class="cls_003"><span class="cls_003">COMMONWEALTH OF PENNSYLVANIA</span></span>
<span style="position:absolute;left:500.25px;top:16px" class="cls_002"><span class="cls_002">REQUEST FOR ORDER FOR</span></span>
<span style="position:absolute;left:47.95px;top:30px" class="cls_003"><span class="cls_003">COUNTY OF ' . strtoupper($courtDetails->county) .'</span></span><br>
<span style="position:absolute;left:570.80px;top:40px" class="cls_002"><span class="cls_002">POSSESSION</span></span>
<span style="position:absolute;left:51px;top:120px" class="cls_004"><span class="cls_004">Mag. Dist. No: '. $courtNumber .'</span></span>
<span style="position:absolute;left:51px;top:134px" class="cls_004"><span class="cls_004">MDJ Name: '. $courtDetails->mdj_name .'</span></span>
<span style="position:absolute;left:445px;top:100px" class="cls_005"><span class="cls_005">PLANTIFF:</span><p style="margin-left:65px;">'. $plantiffName .'<br>'. $plantiffAddress1 .'<br>'. $plantiffAddress2 .'<br>'.$plantiffPhone.'</p></span>
<span style="position:absolute;left:450px;top:185px" class="cls_005"><span class="cls_005">V.</span></span>
<span style="position:absolute;left:450px;top:200px" class="cls_005"><span class="cls_005">DEFENDANT:</span><p style="margin-left:65px;">'. $_POST['tenant_name'].'<br>'.$defendanthouseNum.' '.$defendantStreetName.' '. $_POST['unit_number'] . '<br>'.$defendantTown .',' . $defendantState.' '.$defendantZipcode.'  </p></span>
<span style="position:absolute;left:51px;top:165px" class="cls_004"><span class="cls_004">Address: '.$courtAddressLine1.'<p style="margin-left:49px; margin-top:-4px;">'.$courtAddressLine2.'</p></span></span>
<span style="position:absolute;left:51px;top:205px" class="cls_004"><span class="cls_004">Telephone:</span>'.$courtDetails->phone_number.'</span>
<span style="position:absolute;left:450px;top:310px" class="cls_004"><span class="cls_004">Docket No:</span> '. $_POST['docket_number'] .'</span>
<span style="position:absolute;left:450px;top:325px" class="cls_004"><span class="cls_004">Case Filed:</span></span>
<span style="position:absolute;left:450px;top:340px" class="cls_004"><span class="cls_004">Time Filed:</span></span>
<span style="position:absolute;left:450px;top:355px" class="cls_004"><span class="cls_004">Date Order Filed:</span></span>
<span style="position:absolute;left:135.00px;top:430px" class="cls_004"><span class="cls_004">Judgment Amount</span></span>
<span style="position:absolute;left:235.00px;top:430px" class="cls_003"><span class="cls_003">$</span>'. $_POST['judgment_amount'] .'</span>
<span style="position:absolute;left:60.00px;top:445px" class="cls_004"><span class="cls_004">Costs in Original LT Proceeding</span></span>
<span style="position:absolute;left:235.00px;top:445px" class="cls_003"><span class="cls_003">$</span>'. $_POST['costs_original_lt_proceeding'] .'</span>
<span style="position:absolute;left:105.00px;top:460px" class="cls_004"><span class="cls_004">Costs in this Proceeding</span></span>
<span style="position:absolute;left:235.00px;top:460px" class="cls_003"><span class="cls_003">$</span>'. $_POST['costs_this_proceeding'] .'</span>
<span style="position:absolute;left:157px;top:475px" class="cls_004"><span class="cls_004">Attorney Fees</span></span>
<span style="position:absolute;left:235px;top:475px" class="cls_003"><span class="cls_003">$</span>'. $_POST['attorney_fees'] .'</span>
<span style="position:absolute;left:200px;top:490px" class="cls_004"><span class="cls_004">Total</span></span>
<span style="position:absolute;left:235px;top:490px" class="cls_003"><span class="cls_003">$</span></span>
<span style="position:absolute;left:50px;top:570px" class="cls_004"><span class="cls_004">TO THE MAGISTERIAL DISTRICT JUDGE:</span></span>
<span style="position:absolute;left:50px;top:585px" class="cls_004"><span class="cls_004">The Plaintiff(s) named below, having obtained a judgment for possession of real property located at:</span><br>'.$defendanthouseNum.' '.$defendantStreetName.' '. $_POST['unit_number'] . '<br><br><span style="position:absolute; margin-top:-10px;">'.$defendantTown .',' . $defendantState.' '.$defendantZipcode.'  </span></span>
<span style="position:absolute;left:50px;top:665px" class="cls_004"><span class="cls_004">Address if any:</span></span>
<span style="position:absolute;left:50px;top:720px" class="cls_004"><span class="cls_004">Requests that you issue an ORDER FOR POSSESSION for such property.</span></span>
<span style="position:absolute;left:50px;top:745px" class="cls_004"><span class="cls_004">I certify that this filing complies with the provisions of the Case Records Public Access Policy of the Unified Judicial</span></span>
<span style="position:absolute;left:50px;top:760px" class="cls_004"><span class="cls_004">System of Pennsylvania that require filing confidential information and documents differently than non-confidential</span></span>
<span style="position:absolute;left:50px;top:775px" class="cls_004"><span class="cls_004">information and documents.</span></span>
<span style="position:absolute;left:50px;top:840px" class="cls_004"><span class="cls_004">Plaintiff:</span> '. $plantiffNameBreakpoint .'</span>
<span style="position:absolute;left:427.00px;top:840px" class="cls_004"><span class="cls_004">Date:</span> '. date("d/m/Y") .'</span>
<span style="position:absolute;left:358.00px;top:865px" class="cls_004"><span class="cls_004">Plaintiff Signature:</span><img style="position:absolute; margin-top: -15px; margin-left:10px;" width="140" height="45" src="'.$_POST['signature_source'].'"/></span>
<span style="position:absolute;left:55px;top:985px" class="cls_007"><span class="cls_007">AOPC 311A</span></span>
<span style="position:absolute;left:605px;top:985px" class="cls_008"><span class="cls_008">FREE INTERPRETER</span></span>
<span style="position:absolute;left:590px;top:1000px" class="cls_009"><span class="cls_009">www.pacourts.us/language-rights</span></span><br>
<span style = "position:absolute;left:270px;top:985px" class="cls_008" ><span class="cls_008" > CourtZip ID #'.$evictionId.' </span ></span ><br >
</span></body></html>
');

            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->stream();


            return view('orderOfPossession', compact('map'));
        } catch (\Exception $e) {
            mail('andrew.gaidis@gmail.com', 'formulatePDFCreation Error' . Auth::User()->id, $e->getMessage());
            return back();
        }
    }
}
