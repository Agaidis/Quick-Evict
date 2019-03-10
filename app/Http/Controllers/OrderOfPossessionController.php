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

            $totalFees = $_POST['judgment_amount'] + $_POST['costs_original_lt_proceeding'] + $_POST['costs_this_proceeding'] + $_POST['attorney_fees'];

            $dompdf = new Dompdf();
            $options = new Options();
            $options->setIsRemoteEnabled(true);
            $dompdf->setOptions($options);
            $dompdf->loadHtml('<html>
<head><meta http-equiv=Content-Type content="text/html; charset=UTF-8">
<style type="text/css">
<!--
span.cls_003{font-family:Arial,serif;font-size:13.30px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_003{font-family:Arial,serif;font-size:13.30px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_002{font-family:Arial,serif;font-size:18.75px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_002{font-family:Arial,serif;font-size:18.75px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_004{font-family:Arial,serif;font-size:12.10px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_004{font-family:Arial,serif;font-size:12.10px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_005{font-family:Arial,serif;font-size:9.31px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_005{font-family:Arial,serif;font-size:9.31px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_006{font-family:Arial,serif;font-size:7.98px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_006{font-family:Arial,serif;font-size:7.98px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_007{font-family:Arial,serif;font-size:10.77px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_007{font-family:Arial,serif;font-size:10.77px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_008{font-family:Arial,serif;font-size:10.77px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_008{font-family:Arial,serif;font-size:10.77px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_009{font-family:Arial,serif;font-size:9.31px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: underline}
span.cls_009{font-family:Arial,serif;font-size:9.31px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
--></style></head><body>
<span style="position:absolute;margin-left:-44px;top:-22px;width:787px;height:1112px;overflow:hidden">
<span style="position:absolute;left:0px;top:0px"><img src="https://quickevict.nyc3.digitaloceanspaces.com/oop.jpg" width="800" height="1052"></span>
<span style="position:absolute;left:47.95px;top:16.85px" class="cls_003"><span class="cls_003">COMMONWEALTH OF PENNSYLVANIA</span></span>
<span style="position:absolute;left:364.25px;top:37.95px" class="cls_002"><span class="cls_002">REQUEST FOR ORDER FOR</span></span>
<span style="position:absolute;left:36.05px;top:51.00px" class="cls_003"><span class="cls_003">COUNTY OF ' . strtoupper($courtDetails->county) .'</span></span><br>
<span style="position:absolute;left:412.80px;top:55.20px" class="cls_002"><span class="cls_002">POSSESSION</span></span>
<span style="position:absolute;left:40.64px;top:91.99px" class="cls_004"><span class="cls_004">Mag. Dist. No: '. $courtNumber .'</span></span>
<span style="position:absolute;left:40.90px;top:104.40px" class="cls_004"><span class="cls_004">MDJ Name: '. $courtDetails->mdj_name .'</span></span>
<span style="position:absolute;left:400.90px;top:91.99px" class="cls_005"><span class="cls_005">PLANTIFF:</span><br><p style="margin-left:6px;">'. $plantiffName .'<br>'. $plantiffAddress1 .'<br>'. $plantiffAddress2 .'<br>'.$plantiffPhone.'</p></span>
<span style="position:absolute;left:450.90px;top:113.20px" class="cls_005"><span class="cls_005">V.</span></span>
<span style="position:absolute;left:400.90px;top:150.20px" class="cls_005"><span class="cls_005">DEFENDANT:</span><br><p style="margin-left:6px;">'. $_POST['tenant_name'].'<br>'.$defendanthouseNum.' '.$defendantStreetName.' '. $_POST['unit_number'] . '<br>'.$defendantTown .',' . $defendantState.' '.$defendantZipcode.'  </p></span>
<span style="position:absolute;left:40.90px;top:122.60px" class="cls_004"><span class="cls_004">Address: '.$courtAddressLine1.'<br><span style="margin-left:45px;">'.$courtAddressLine2.'</span></span>
<span style="position:absolute;left:40.90px;top:165.30px" class="cls_004"><span class="cls_004">Telephone:</span></span>
<span style="position:absolute;left:395.89px;top:210.80px" class="cls_004"><span class="cls_004">Docket No:</span></span>
<span style="position:absolute;left:394.89px;top:225.80px" class="cls_004"><span class="cls_004">Case Filed:</span></span>
<span style="position:absolute;left:395.84px;top:240.80px" class="cls_004"><span class="cls_004">Time Filed:</span></span>
<span style="position:absolute;left:371.27px;top:255.80px" class="cls_004"><span class="cls_004">Date Order Filed:</span></span>
<span style="position:absolute;left:108.00px;top:327.75px" class="cls_004"><span class="cls_004">Judgment Amount</span></span>
<span style="position:absolute;left:192.00px;top:327.55px" class="cls_003"><span class="cls_003">$_</span>'. $_POST['judgment_amount'] .'</span>
<span style="position:absolute;left:290.68px;top:327.55px" class="cls_003"><span class="cls_003">_</span></span>
<span style="position:absolute;left:54.00px;top:339.75px" class="cls_004"><span class="cls_004">Costs in Original LT Proceeding</span></span>
<span style="position:absolute;left:192.00px;top:339.55px" class="cls_003"><span class="cls_003">$_</span>'. $_POST['costs_original_lt_proceeding'] .'</span>
<span style="position:absolute;left:290.68px;top:339.55px" class="cls_003"><span class="cls_003">_</span></span>
<span style="position:absolute;left:84.00px;top:351.75px" class="cls_004"><span class="cls_004">Costs in this Proceeding</span></span>
<span style="position:absolute;left:192.00px;top:351.55px" class="cls_003"><span class="cls_003">$_</span>'. $_POST['costs_this_proceeding'] .'</span>
<span style="position:absolute;left:290.68px;top:351.55px" class="cls_003"><span class="cls_003">_</span></span>
<span style="position:absolute;left:124.85px;top:364.05px" class="cls_004"><span class="cls_004">Attorney Fees</span></span>
<span style="position:absolute;left:192.00px;top:363.55px" class="cls_003"><span class="cls_003">$_</span>'. $_POST['attorney_fees'] .'</span>
<span style="position:absolute;left:290.68px;top:363.55px" class="cls_003"><span class="cls_003">_</span></span>
<span style="position:absolute;left:162.00px;top:393.75px" class="cls_006"><span class="cls_006">Total</span></span>
<span style="position:absolute;left:192.00px;top:393.55px" class="cls_003"><span class="cls_003">$_</span>'. $totalFees .'</span>
<span style="position:absolute;left:290.68px;top:393.55px" class="cls_003"><span class="cls_003">_</span></span>
<span style="position:absolute;left:36.00px;top:423.75px" class="cls_004"><span class="cls_004">TO THE MAGISTERIAL DISTRICT JUDGE:</span></span>
<span style="position:absolute;left:36.00px;top:435.00px" class="cls_004"><span class="cls_004">The Plaintiff(s) named below, having obtained a judgment for possession of real property located at:</span>'.$defendanthouseNum.' '.$defendantStreetName.' '. $_POST['unit_number'] . '<br>'.$defendantTown .',' . $defendantState.' '.$defendantZipcode.'  </p></span>
<span style="position:absolute;left:36.00px;top:493.25px" class="cls_004"><span class="cls_004">Address if any:</span></span>
<span style="position:absolute;left:36.00px;top:544.14px" class="cls_004"><span class="cls_004">Requests that you issue an ORDER FOR POSSESSION for such property.</span></span>
<span style="position:absolute;left:36.40px;top:565.11px" class="cls_004"><span class="cls_004">I certify that this filing complies with the provisions of the Case Records Public Access Policy of the Unified Judicial</span></span>
<span style="position:absolute;left:36.40px;top:575.91px" class="cls_004"><span class="cls_004">System of Pennsylvania that require filing confidential information and documents differently than non-confidential</span></span>
<span style="position:absolute;left:36.40px;top:586.71px" class="cls_004"><span class="cls_004">information and documents.</span></span>
<span style="position:absolute;left:36.70px;top:632.55px" class="cls_004"><span class="cls_004">Plaintiff:</span> '. $plantiffName .'</span>
<span style="position:absolute;left:400.00px;top:802.40px" class="cls_004"><span class="cls_004">Date:</span> '. date("d/m/Y") .'</span>
<span style="position:absolute;left:420.00px;top:855.55px" class="cls_004"><span class="cls_004">Plaintiff Signature:</span><img style="position:absolute; top:-60px" width="160" height="65" src="'.$_POST['signature_source'].'"/></span>
<span style="position:absolute;left:55.40px;top:985.85px" class="cls_007"><span class="cls_007">AOPC 311A</span></span>
<span style="position:absolute;left:605.75px;top:985.50px" class="cls_008"><span class="cls_008">FREE INTERPRETER</span></span>
<span style="position:absolute;left:590.75px;top:1000.50px" class="cls_009"><span class="cls_009">www.pacourts.us/language-rights</span></span><br>
<span style = "position:absolute;left:303.75px;top:985.50px" class="cls_008" ><span class="cls_008" > CourtZip ID # </span ></span ><br >
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
