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
                'geoData' => $geoData
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

            try {
                $eviction = new Evictions();
                $eviction->status = 'Created OOP';
                $eviction->property_address = $defendanthouseNum.' '.$defendantStreetName.'-1'.$defendantTown .',' . $defendantState.' '.$defendantZipcode;
                $eviction->tenant_name = $_POST['tenant_name'];
                $eviction->total_judgement = '0';
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

                $eviction->save();

                $evictionId = $eviction->id;
            } catch ( \Exception $e) {
                Log::info($e);
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
span.cls_005{font-family:Arial,serif;font-size:9px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_006{font-family:Arial,serif;font-size:8px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_007{font-family:Arial,serif;font-size:12px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_008{font-family:Arial,serif;font-size:10px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_009{font-family:Arial,serif;font-size:9px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_010{font-family:Arial,serif;font-size:10px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_011{font-family:Arial,serif;font-size:9px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: underline}
--></style></head><body>
<span style="position:absolute;margin-left:-44px;top:-22px;width:787px;height:1112px;overflow:hidden">
<span style="position:absolute;left:0px;top:0px"><img src="https://quickevict.nyc3.digitaloceanspaces.com/civilcomplaint.jpg" width="800" height="1052"></span>
<span style="position:absolute;left:48px;top:16px" class="cls_003"><span class="cls_003">COMMONWEALTH OF PENNSYLVANIA</span></span>
<span style="position:absolute;left:450px;top:16px" class="cls_002"><span class="cls_002">CIVIL COMPLAINT</span></span>
<span style="position:absolute;left:48px;top:35px" class="cls_003"><span class="cls_003">COUNTY OF ' . strtoupper($courtDetails->county) .'</span></span>
<span style="position:absolute;left:400px;top:120px" class="cls_010"><span class="cls_010">PLAINTIFF:</span><br><p style="margin-left:6px;">'. $plantiffName .'<br>'. $plantiffAddress1 .'<br>'. $plantiffAddress2 .'<br>'.$plantiffPhone.'</p></span><br>
<span style="position:absolute;left:464px;top:87px" class="cls_005"><span class="cls_005">NAME and ADDRESS</span></span>
<span style="position:absolute;left:51px;top:120px" class="cls_004"><span class="cls_004">Mag. Dist. No: '. $courtNumber .'</span></span><br>
<span style="position:absolute;left:51px;top:134px" class="cls_004"><span class="cls_004">MDJ Name: '. $courtDetails->mdj_name .'</span></span><br>
<span style="position:absolute;left:55px;top:120px" class="cls_004"><span class="cls_004">Address: '.$courtAddressLine1.'<br><span style="margin-left:45px;">'.$courtAddressLine2.'</span></span></span><br>
<span style="position:absolute;left:581px;top:184px" class="cls_006"><span class="cls_006">V.</span></span>
<span style="position:absolute;left:447px;top:181px" class="cls_010"><span class="cls_010">DEFENDANT:</span><br><p style="margin-left:6px;">'.$_POST['tenant_name'].'<br>'.$defendanthouseNum.' '.$defendantStreetName.' '. $_POST['unit_number'] . '<br>'.$defendantTown .',' . $defendantState.' '.$defendantZipcode.'  </p></span><br>
<span style="position:absolute;left:601px;top:180px" class="cls_005"><span class="cls_005">NAME and ADDRESS</span></span><br>
<span style="position:absolute;left:55px;top:188px" class="cls_004"><span class="cls_004">Telephone: '.$courtDetails->phone_number.'</span></span><br>
<span style="position:absolute;left:195px;top:215px" class="cls_004"><span class="cls_004">AMOUNT</span></span><br>
<span style="position:absolute;left:293px;top:215px" class="cls_004"><span class="cls_004">DATE PAID</span></span><br>
<span style="position:absolute;left:55px;top:235px" class="cls_004"><span class="cls_004">FILING COSTS</span></span><br>
<span style="position:absolute;left:152px;top:235px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:55px;top:253px" class="cls_004"><span class="cls_004">POSTAGE</span></span><br>
<span style="position:absolute;left:152px;top:253px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:55px;top:271px" class="cls_004"><span class="cls_004">SERVICE COSTS</span></span><br>
<span style="position:absolute;left:152px;top:271px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:349.92px;top:221.18px" class="cls_004"><span class="cls_004">Docket No:</span></span><br>
<span style="position:absolute;left:38.80px;top:233.70px" class="cls_004"><span class="cls_004">CONSTABLE ED.</span></span><br>
<span style="position:absolute;left:120.00px;top:233.70px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:481px;top:273px" class="cls_004"><span class="cls_004">Case Filed:</span></span><br>
<span style="position:absolute;left:38.80px;top:252.20px" class="cls_004"><span class="cls_004">TOTAL</span></span><br>
<span style="position:absolute;left:120.00px;top:252.20px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:55px;top:336px" class="cls_003"><span class="cls_003">Pa.R.C.P.M.D.J. No. 206 sets forth those costs recoverable by the prevailing party.</span></span>
<span style="position:absolute;left:60px;top:356px" class="cls_003"><span class="cls_003">To The Defendant:  The above named plaintiff(s) asks judgment against you for $__________________ together with costs</span></span>
<span style="position:absolute;left:126.00px;top:356px" class="cls_003"><span class="cls_003">upon the following claim (Civil fines must include citation of the statute or ordinance violated):</span></span>
<span style="position:absolute;left:36.00px;top:470.80px" class="cls_003"><span class="cls_003">I, ____________'.$_POST['tenant_name'].'______________ verify that the facts set forth in this complaint are true and correct to the</span></span>
<span style="position:absolute;left:36.00px;top:482.80px" class="cls_003"><span class="cls_003">best of my knowledge, information, and belief.  This statement is made subject to the penalties of Section 4904 of the</span></span>
<span style="position:absolute;left:36.00px;top:494.80px" class="cls_003"><span class="cls_003">Crimes Code (18 PA. C.S. § 4904) related to unsworn falsification to authorities.</span></span>
<span style="position:absolute;left:36.57px;top:512.49px" class="cls_003"><span class="cls_003">I certify that this filing complies with the provisions of the Case Records Public Access Policy of the Unified Judicial System</span></span>
<span style="position:absolute;left:36.57px;top:524.43px" class="cls_003"><span class="cls_003">of Pennsylvania that require filing confidential information and documents differently than non-confidential information and</span></span>
<span style="position:absolute;left:36.57px;top:536.37px" class="cls_003"><span class="cls_003">documents.</span></span>
<span style="position:absolute;left:352.10px;top:575.50px" class="cls_004"><img style="position:absolute; top:-60px" width="160" height="65" src="'.$_POST['signature_source'].'"/><span class="cls_004">(Signature of Plaintiff or Authorized Agent)</span></span>
<span style="position:absolute;left:36.00px;top:595.20px" class="cls_004"><span class="cls_004">The plaintiff\'s attorney shall file an entry of appearance with the magisterial district court pursuant to Pa.R.C.P.M.D.J. 207.1</span></span>
<span style="position:absolute;left:36.50px;top:638.55px" class="cls_007"><span class="cls_007">If you intend to enter a defense to this complaint, you should notify this office immediately at the above telephone number.  You</span></span>
<span style="position:absolute;left:36.50px;top:649.80px" class="cls_007"><span class="cls_007">must appear at the hearing and present your defense.  Unless you do, judgment may be entered against you by default.</span></span>
<span style="position:absolute;left:36.00px;top:672.05px" class="cls_004"><span class="cls_004">If you have a claim against the plaintiff which is within the magisterial district judge jurisdiction and which you intend to assert at the</span></span>
<span style="position:absolute;left:36.00px;top:683.30px" class="cls_004"><span class="cls_004">hearing, you must file it on a complaint form at this office at least five days before the date set for the hearing.</span></span>
<span style="position:absolute;left:36.00px;top:703.25px" class="cls_007"><span class="cls_007">If you are disabled and require a reasonable accommodation to gain access to the Magisterial District Court and its services,</span></span>
<span style="position:absolute;left:36.00px;top:714.50px" class="cls_007"><span class="cls_007">please contact the Magisterial District Court at the above address or telephone number.  We are unable to provide</span></span>
<span style="position:absolute;left:36.00px;top:725.75px" class="cls_007"><span class="cls_007">transportation.</span></span>
<span style="position:absolute;left:55px;top:985px" class="cls_008"><span class="cls_008">AOPC 308A</span></span>
<span style="position:absolute;left:605px;top:985px" class="cls_009"><span class="cls_009">FREE INTERPRETER</span></span>
<span style="position:absolute;left:590px;top:1000px" class="cls_011"><span class="cls_011"> </span><A HREF="http://www.pacourts.us/language-rights/">www.pacourts.us/language-rights</A> </span>
<span style = "position:absolute;left:270px;top:985px" class="cls_008" ><span class="cls_008" > CourtZip ID #'.$evictionId.' </span ></span ><br >
</span></body></html>
');

            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->stream();


            return view('civilComplaint', compact('map'));
        } catch (\Exception $e) {
            Log:info($e);
            mail('andrew.gaidis@gmail.com', 'formulatePDFCreation  Civil Complaint Error' . Auth::User()->id, $e->getMessage());
            return back();
        }
    }
}
