<?php

namespace App\Http\Controllers;

use Dompdf\Options;
use GMaps;
use Dompdf\Dompdf;
use App\CourtDetails;
use Illuminate\Support\Facades\DB;



class EvictionController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('eviction', compact('map'));
    }

    public function formulatePDF() {

        try {
            $courtNumber = $_POST['court_number'];
            $courtDetails = CourtDetails::where('court_number', $courtNumber)->first();

            $courtPhone = $_POST['court_phone_number'];
            $courtAddressLine1 = $_POST['court_address1'];
            $courtAddressLine2 = $_POST['court_address2'];

            $attorneyFees = $_POST['attorney_fees'];
            $attorneyFees = str_replace('$', '', $attorneyFees);

            $damageAmt = $_POST['damage_amt'];
            $damageAmt = str_replace('$', '', $damageAmt);

            $dueRent = $_POST['due_rent'];
            $dueRent = str_replace('$', '', $dueRent);

            $securityDeposit = $_POST['security_deposit'];
            $securityDeposit = str_replace('$', '', $securityDeposit);

            $monthlyRent = $_POST['monthly_rent'];
            $monthlyRent = str_replace('$', '', $monthlyRent);

            $additionalRent = $_POST['addit_rent'];
            $filing_date = $_POST['filing_date'];

            $tenantName = $_POST['tenant_name'];
            $landlord = $_POST['landlord'];

            if (isset($_POST['tenant_num'])) {
                $upTo2000 = $courtDetails->one_defendant_up_to_2000;
                $btn20014000 = $courtDetails->one_defendant_between_2001_4000;
                $greaterThan4000 = $courtDetails->one_defendant_greater_than_4000;
                $oop = $courtDetails->one_defendant_out_of_pocket;
            } else {
                $upTo2000 = $courtDetails->two_defendant_up_to_2000;
                $btn20014000 = $courtDetails->two_defendant_between_2001_4000;
                $greaterThan4000 = $courtDetails->two_defendant_greater_than_4000;
                $oop = $courtDetails->two_defendant_out_of_pocket;
            }





            //Lease Type
            $leaseType = $_POST['lease_type'];
            if ($leaseType == 'isResidential') {
                $isResidential = '<input type="checkbox" checked/>';
                $isNotResidential = '<input type="checkbox"/>';
            } else {
                $isResidential = '<input type="checkbox"/>';
                $isNotResidential = '<input type="checkbox" checked/>';
            }

            //Notice Status
            $quitNotice = $_POST['quit_notice'];
            if ($quitNotice == 'no_quit_notice') {
                $noQuitNotice = '<input type="checkbox" checked/>';
                $quitNoticeGiven = '<input type="checkbox"/>';
            } else {
                $noQuitNotice = '<input type="checkbox"/>';
                $quitNoticeGiven = '<input type="checkbox" checked/>';
            }

            //Lease Status
            if (isset($_POST['unsatisfied_lease'])) {
                $unsatisfiedLease = '<input type="checkbox" checked/>';
            } else {
                $unsatisfiedLease = '<input type="checkbox"/>';
            }
            if (isset($_POST['breached_conditions_lease'])) {
                $breachedConditionsLease = '<input type="checkbox" checked/>';
            } else {
                $breachedConditionsLease = '<input type="checkbox"/>';
            }
            $breachedDetails = $_POST['breached_details'];

            if (isset($_POST['term_lease_ended'])) {
                $leaseEnded = '<input type="checkbox" checked/>';
            } else {
                $leaseEnded = '<input type="checkbox" checked/>';
            }

            $ownerName = $_POST['owner_name'];
            $ownerPhone = $_POST['owner_phone'];

            $rentedBy = $_POST['rented_by'];

            $unjustDamages = $_POST['unjust_damages'];
            $unjustDamages = str_replace('$', '', $unjustDamages);

            $defendantState = $_POST['state'];
            $defendantZipcode = $_POST['zipcode'];
            $defendantCounty = $_POST['county'];
            $defendanthouseNum = $_POST['houseNum'];
            $defendantStreetName= $_POST['streetName'];



            $totalFees = (int)$attorneyFees + (int)$dueRent + (int)$unjustDamages + (int)$damageAmt;

            if ($totalFees < 2000) {
                $filingFee = $upTo2000;
            } else if ($totalFees >= 2000 && $totalFees <= 4000) {
                $filingFee = $btn20014000;
            } else if ($totalFees > 4000) {
                $filingFee = $greaterThan4000;
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
span.cls_003{font-family:Arial,serif;font-size:10.0px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_003{font-family:Arial,serif;font-size:10.0px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_002{font-family:Arial,serif;font-size:14.1px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_002{font-family:Arial,serif;font-size:14.1px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_005{font-family:Arial,serif;font-size:7.0px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_005{font-family:Arial,serif;font-size:7.0px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_004{font-family:Arial,serif;font-size:9.1px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_004{font-family:Arial,serif;font-size:9.1px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_006{font-family:Arial,serif;font-size:6.0px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_006{font-family:Arial,serif;font-size:6.0px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_009{font-family:Arial,serif;font-size:7.0px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: underline}
span.cls_009{font-family:Arial,serif;font-size:7.0px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_007{font-family:Arial,serif;font-size:8.1px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_007{font-family:Arial,serif;font-size:8.1px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_008{font-family:Arial,serif;font-size:8.1px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_008{font-family:Arial,serif;font-size:8.1px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_010{font-family:Arial,serif;font-size:8.1px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: underline}
span.cls_010{font-family:Arial,serif;font-size:8.1px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
--></style></head><body>
<span style="position:absolute;left:50%;margin-left:-306px;top:0px;width:612px;height:792px;border-style:outset;overflow:hidden">
<span style="position:absolute;left:0px;top:0px"><img src="https://quickevict.nyc3.digitaloceanspaces.com/background1.jpg" width="612" height="792"></span>
<span style="position:absolute;left:36.05px;top:16.85px" class="cls_003"><span class="cls_003">COMMONWEALTH OF PENNSYLVANIA</span></span><br>
<span style="position:absolute;left:346.20px;top:16.80px" class="cls_002"><span class="cls_002">LANDLORD/TENANT COMPLAINT</span></span><br>
<span style="position:absolute;left:36.05px;top:29.55px" class="cls_003"><span class="cls_003">COUNTY OF ' . strtoupper($courtDetails->county) .'</span></span><br>
<span style="position:absolute;left:336.30px;top:67.80px" class="cls_005"><span class="cls_005">PLAINTIFF:</span><br><p style="margin-left:6px;">SlateHouse Group Property Management LLC on behalf of '.$ownerName.'<br>PO Box 5304<br>Lancaster, PA 17606</p></span><br>
<span style="position:absolute;left:463.70px;top:68.50px" class="cls_005"><span class="cls_005">NAME and ADDRESS</span></span>
<span style="position:absolute;left:40.80px;top:69.36px" class="cls_004"><span class="cls_004">Mag. Dist. No: '. $courtDetails->court_number.'</span></span><br>
<span style="position:absolute;left:40.90px;top:82.85px" class="cls_004"><span class="cls_004">MDJ Name: '. $courtDetails->mdj_name .'</span></span><br>
<span style="position:absolute;left:40.90px;top:101.05px" class="cls_004"><span class="cls_004">Address: '.$courtAddressLine1.'<br><span style="margin-left:34px;">'.$courtAddressLine2.'</span></span></span><br>
<span style="position:absolute;left:437.10px;top:130.90px" class="cls_006"><span class="cls_006">V.</span></span><br>
<span style="position:absolute;left:336.30px;top:133.60px" class="cls_009"><span class="cls_009">DEFENDANT:</span><br><p style="margin-left:6px;">'.$tenantName.'<br>'.$defendanthouseNum.' '.$defendantStreetName.'<br>'.$defendantCounty.', '.$defendantState.' '.$defendantZipcode.'</p></span><br>
<span style="position:absolute;left:466.50px;top:135.00px" class="cls_005"><span class="cls_005">NAME and ADDRESS</span></span><br>
<span style="position:absolute;left:40.90px;top:144.45px" class="cls_004"><span class="cls_004">Telephone: '.$courtPhone.'</span></span><br>
<span style="position:absolute;left:142.45px;top:160.95px" class="cls_004"><span class="cls_004">AMOUNT</span></span><br>
<span style="position:absolute;left:229.35px;top:160.95px" class="cls_004"><span class="cls_004">DATE PAID</span></span><br>
<span style="position:absolute;left:38.80px;top:174.00px" class="cls_004"><span class="cls_004">FILING COSTS:</span></span><br>
<span style="position:absolute;left:120.00px;top:174.00px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:38.80px;top:187.55px" class="cls_004"><span class="cls_004">POSTAGE</span></span><br>
<span style="position:absolute;left:120.00px;top:187.55px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:382.55px;top:195.45px" class="cls_004"><span class="cls_004">Docket No: </span></span><br>
<span style="position:absolute;left:38.80px;top:201.10px" class="cls_004"><span class="cls_004">SERVICE COSTS</span></span><br>
<span style="position:absolute;left:120.00px;top:201.10px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:381.45px;top:208.85px" class="cls_004"><span class="cls_004">Case Filed:</span></span><br>
<span style="position:absolute;left:38.80px;top:214.65px" class="cls_004"><span class="cls_004">CONSTABLE ED.</span></span><br>
<span style="position:absolute;left:120.00px;top:214.65px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:38.80px;top:233.15px" class="cls_004"><span class="cls_004">TOTAL</span></span><br>
<span style="position:absolute;left:120.25px;top:233.15px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:36.00px;top:252.35px" class="cls_003"><span class="cls_003">Pa.R.C.P.M.D.J. No. 206 sets forth those costs recoverable by the prevailing party.</span></span><br>
<span style="position:absolute;left:42.00px;top:267.85px" class="cls_004"><span class="cls_004">TO THE DEFENDANT: The above named plaintiff(s) asks judgment together with costs against you for the possession of real</span></span><br>
<span style="position:absolute;left:62.77px;top:279.51px" class="cls_004"><span class="cls_004">property and for:</span></span><br>
<span style="position:absolute;left:60.87px;top:292.21px" class="cls_004"><span class="cls_004">Lease is</span></span><br>
<span style="position:absolute;left:120.25px;top:292.21px" class="cls_004"><span class="cls_004">'. $isResidential .'Residential</span></span><br>
<span style="position:absolute;left:198.23px;top:292.21px" class="cls_004"><span class="cls_004">'. $isNotResidential .'Nonresidential     Monthly Rent  $</span>'.$monthlyRent.'</span><br>
<span style="position:absolute;left:415.98px;top:292.21px" class="cls_004"><span class="cls_004">Security Deposit $</span>'.$securityDeposit.'</span><br>
<span style="position:absolute;left:60.87px;top:304.91px" class="cls_004"><span class="cls_004"><input type="checkbox"  />A determination that the manufactured home and property have been abandoned.</span></span><br>
<span style="position:absolute;left:60.87px;top:317.61px" class="cls_004"><span class="cls_004"><input type="checkbox"  />A Request for Determination of Abandonment (Form MDJS 334) must be completed and submitted with this complaint.</span></span><br>
<span style="position:absolute;left:61.30px;top:332.71px" class="cls_004"><span class="cls_004"><input type="checkbox"  />Damages for injury to the real property, to wit: __________________________________________________________________</span></span><br>
<span style="position:absolute;left:60.87px;top:348.21px" class="cls_004"><span class="cls_004">______________________________________________________________  in the amount of:</span></span><br>
<span style="position:absolute;left:457.40px;top:348.45px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:466.55px;top:348.21px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________'.$damageAmt.'_________</span></span><br>
<span style="position:absolute;left:60.50px;top:363.95px" class="cls_004"><span class="cls_004"><input type="checkbox"  />Damages for the unjust detention of the real property in the amount of</span></span><br>
<span style="position:absolute;left:457.40px;top:363.95px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:465.42px;top:363.95px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________'.$unjustDamages.'_________</span></span><br>
<span style="position:absolute;left:60.50px;top:379.45px" class="cls_004"><span class="cls_004"><input type="checkbox"  />Rent remaining due and unpaid on filing date in the amount of</span></span><br>
<span style="position:absolute;left:457.40px;top:379.45px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:465.42px;top:379.45px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________'.$dueRent.'_________</span></span><br>
<span style="position:absolute;left:60.50px;top:395.95px" class="cls_004"><span class="cls_004"><input type="checkbox"  />And additional rent remaining due and unpaid on hearing date</span></span><br>
<span style="position:absolute;left:457.40px;top:395.95px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:465.42px;top:395.95px" class="cls_004"><span class="cls_004">___________________</span></span><br>
<span style="position:absolute;left:60.50px;top:410.45px" class="cls_004"><span class="cls_004"><input type="checkbox"  />Attorney fees in the amount of</span></span><br>
<span style="position:absolute;left:457.40px;top:410.45px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:465.42px;top:410.45px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________'.$attorneyFees.'_________</span></span><br>
<span style="position:absolute;left:42.30px;top:427.20px" class="cls_004"><span class="cls_004">THE PLAINTIFF FURTHER ALLEGES THAT:</span></span><br>
<span style="position:absolute;left:423.80px;top:427.20px" class="cls_004"><span class="cls_004">Total:</span></span><br>
<span style="position:absolute;left:457.40px;top:427.20px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:465.42px;top:427.20px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________'.$totalFees.'_________</span></span><br>
<span style="position:absolute;left:42.30px;top:442.15px" class="cls_004"><span class="cls_004">1. The location and the address, if any, of the real property is:</span></span><br>
<span style="position:absolute;left:293.85px;top:442.15px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">'.$defendanthouseNum.' '.$defendantStreetName.' '.$defendantCounty.', '.$defendantState.' '.$defendantZipcode.'</span></span><br>
<span style="position:absolute;left:42.30px;top:454.05px" class="cls_004"><span class="cls_004">2. The plaintiff is the landlord of that property.</span></span><br>
<span style="position:absolute;left:42.30px;top:464.55px" class="cls_004"><span class="cls_004">3. The plaintiff leased or rented the property to you or to ___________________________________________under whom you claim</span></span><br>
<span style="position:absolute;left:42.30px;top:478.65px" class="cls_004"><span class="cls_004">4.</span></span><br>
<span style="position:absolute;left:76.60px;top:478.65px" class="cls_004"><span class="cls_004">'.$quitNoticeGiven.'Notice to quit was given in accordance with law, or</span></span><br>
<span style="position:absolute;left:76.60px;top:494.15px" class="cls_004"><span class="cls_004">'.$noQuitNotice.'No notice is required under the terms of the lease.</span></span><br>
<span style="position:absolute;left:42.30px;top:513.45px" class="cls_004"><span class="cls_004">5.</span></span><br>  
<span style="position:absolute;left:77.30px;top:513.45px" class="cls_004"><span class="cls_004">'.$leaseEnded.'The term for which the property was leased or rented is fully ended, or</span></span><br>
<span style="position:absolute;left:77.30px;top:531.35px" class="cls_004"><span class="cls_004">'.$breachedConditionsLease.'A forfeiture has resulted by reason of a breach of the conditions of the lease, to wit:</span></span><br>
<span style="position:absolute;left:414.74px;top:531.35px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">'.$breachedDetails.'_____</span></span>
<span style="position:absolute;left:77.30px;top:541.35px" class="cls_004"><span class="cls_004">________________________________________________________________________________________________or,</span></span><br>
<span style="position:absolute;left:77.30px;top:554.15px" class="cls_004"><span class="cls_004">___________________________________________________________________________________________________</span></span><br>
<span style="position:absolute;left:77.30px;top:569.55px" class="cls_004"><span class="cls_004">'.$unsatisfiedLease.'Rent reserved and due has, upon demand, remained unsatisfied.</span></span><br>
<span style="position:absolute;left:42.30px;top:582.15px" class="cls_004"><span class="cls_004">6.</span></span><br>
<span style="position:absolute;left:60.50px;top:582.15px" class="cls_004"><span class="cls_004">You retain the real property and refuse to give up to its possession.</span></span><br>
<span style="position:absolute;left:42.00px;top:595.65px" class="cls_004"><span class="cls_004">I, ________________________________________________________________ verify that the facts set forth in this complaint are</span></span><br>
<span style="position:absolute;left:42.00px;top:605.85px" class="cls_004"><span class="cls_004">true and correct to the best of my knowledge, information and belief. This statement is made subject to the penalties of Section 4904</span></span><br>
<span style="position:absolute;left:42.00px;top:616.05px" class="cls_004"><span class="cls_004">of the Crimes Code (18 PA. C.S. § 4904) relating to unsworn falsification to authorities.</span></span><br>
<span style="position:absolute;left:42.30px;top:630.90px" class="cls_004"><span class="cls_004">I certify this filing complies with the UJS Case Records Public Access Policy.</span></span><br>
<span style="position:absolute;left:440.00px;top:656.80px" class="cls_004"><span class="cls_004">(Signature of Plaintiff)</span></span><br>
<span style="position:absolute;left:48.00px;top:669.40px" class="cls_004"><span class="cls_004">The plaintiff\'s attorney shall file an entry of appearance with the magisterial district court pursuant to Pa . R . C . P . M . D . J . 207.1 </span ></span ><br >
<span style = "position:absolute;left:47.90px;top:685.15px" class="cls_005" ><span class="cls_005" >IF YOU HAVE A DEFENSE to this complaint you may present it at the hearing . IF YOU HAVE A CLAIM against the plaintiff arising out of the occupancy of the premises,</span ></span ><br >
<span style = "position:absolute;left:47.90px;top:693.30px" class="cls_005" ><span class="cls_005" > which is in the magisterial district judge jurisdiction and which you intend to assert at the hearing, YOU MUST FILE it on the complaint form at the office BEFORE THE TIME </span ></span ><br >
<span style = "position:absolute;left:47.90px;top:701.45px" class="cls_005" ><span class="cls_005" > set for the hearing . IF YOU DO NOT APPEAR AT THE HEARING, a judgment for possession and costs, and for damages and rent if claimed, may nevertheless be entered </span ></span ><br >
<span style = "position:absolute;left:47.90px;top:709.60px" class="cls_005" ><span class="cls_005" > against you . A judgment against you for possession may result in your EVICTION from the premises .</span ></span ><br >
<span style = "position:absolute;left:47.90px;top:717.75px" class="cls_007" ><span class="cls_007" >If you are disabled and require a reasonable accommodation to gain access to the Magisterial District Court and its services, please </span ></span ><br >
<span style = "position:absolute;left:47.90px;top:727.35px" class="cls_007" ><span class="cls_007" > contact the Magisterial District Court at the above address or telephone number . We are unable to provide transportation .</span ></span ><br >
<span style = "position:absolute;left:36.00px;top:741.85px" class="cls_008" ><span class="cls_008" > AOPC 310A </span ></span ><br >
<span style = "position:absolute;left:303.75px;top:742.50px" class="cls_008" ><span class="cls_008" > 1</span ></span ><br >
<span style = "position:absolute;left:471.65px;top:742.10px" class="cls_007" ><span class="cls_007" > </span >Filing Fee: $'.$filingFee.'</span ><br >
<span style = "position:absolute;left:452.45px;top:748.80px" class="cls_010" ><span class="cls_010" > </span ></span >
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