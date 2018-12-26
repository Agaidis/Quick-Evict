<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Evictions;
use Dompdf\Options;
use App\CourtDetails;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //    $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $evictions = DB::select('select * from evictions ORDER BY FIELD(status, "Created LTC", "LTC Mailed", "LTC Submitted Online", "Court Hearing Scheduled", "Court Hearing Extended", "Judgement Issued in Favor of Owner", "Judgement Denied by Court", "Tenant Filed Appeal", "OOP Mailed", "OOP Submitted Online", "Paid Judgement", "Locked Out Tenant"), id DESC');

            return view('dashboard' , compact('evictions'));
        } catch (\Exception $e) {
            $errorDetails = 'DashboardController - error in store() method when attempting to store magistrate';
            $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
            $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();
            \Log::error($errorDetails . PHP_EOL . 'Error Message: ' . $e->getMessage() . PHP_EOL . 'Trace: ' . $e->getTraceAsString());
            mail('andrew.gaidis@gmail.com', 'Showing Home Page', $errorDetails);
        }

    }

    public function statusChange(Request $request) {
        try {
            $eviction = Evictions::find($request->id);
            $eviction->status = $request->status;

            $eviction->save();

            return 'success';
        } catch (\Exception $e) {
            $errorDetails = 'DashboardController - error in store() method when attempting to store magistrate';
            $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
            $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();
            \Log::error($errorDetails . PHP_EOL . 'Error Message: ' . $e->getMessage() . PHP_EOL . 'Trace: ' . $e->getTraceAsString());
            mail('andrew.gaidis@gmail.com', 'Changing Status', $errorDetails);
        }
    }

    public function downloadPDF(Request $request)
    {
        try {
            $evictionData = Evictions::where('id', $request->id)->first();
            $courtDetails = CourtDetails::where('magistrate_id', $evictionData->magistrate_id)->first();

            $evictionId = $evictionData->id;
            $courtNumber = $evictionData->court_number;
            $courtAddressLine1 = $evictionData->court_address_line_1;
            $courtAddressLine2 = $evictionData->court_address_line_2;
            $attorneyFees = $evictionData->attorney_fees;
            $damageAmt = $evictionData->damage_amt;
            $dueRent = $evictionData->due_rent;
            $securityDeposit = $evictionData->security_deposit;
            $monthlyRent = $evictionData->monthly_rent;
            $unjustDamages = $evictionData->unjust_damages;
            $tenantName = $evictionData->tenant_name;
            $ownerName = $evictionData->owner_name;
            $breachedDetails = $evictionData->breached_details;
            $propertyDamageDetails = $evictionData->property_damage_details;
            $plaintiffLine = $evictionData->plaintiff_line;
            $isResidential = $evictionData->is_residential;
            $noQuitNotice = $evictionData->no_quit_notice;
            $unsatisfiedLease = $evictionData->unsatisfied_lease;
            $breachedConditionsLease = $evictionData->breached_conditions_lease;
            $isAmtGreaterThanZero = $evictionData->amt_greater_than_zero;
            $leaseEnded = $evictionData->lease_ended;
            $defendantState = $evictionData->defendant_state;
            $defendantZipcode = $evictionData->defendant_zipcode;
            $defendantHouseNum = $evictionData->defendant_house_num;
            $defendantStreetName = $evictionData->defendant_street_name;
            $defendantTown = $evictionData->defendant_town;
            $filingFee = $evictionData->filing_fee;
            $totalFees = $evictionData->total_judgement;
            $pmName = $evictionData->pm_name;
            $pmPhone = $evictionData->pm_phone;
            $isAbandoned = $evictionData->is_abandoned;
            $isDeterminationRequest = $evictionData->is_determination_request;
            $isAdditionalRent = $evictionData->is_additional_rent;
            $unitNum = $evictionData->unit_num;


            if ($attorneyFees > 0) {
                $attorneyFeesCheckbox = '<input type="checkbox" checked/>';
            } else {
                $attorneyFeesCheckbox = '<input type="checkbox"/>';
            }

            if ($isAmtGreaterThanZero == true) {
                $amtGreaterThanZeroCheckbox = '<input type="checkbox" checked/>';
            } else {
                $amtGreaterThanZeroCheckbox = '<input type="checkbox"/>';
            }

            if ($damageAmt != '') {
                $damageAmtCheckbox = '<input type="checkbox" checked/>';
            } else {
                $damageAmtCheckbox = '<input type="checkbox"/>';
            }

            if ($unjustDamages != '') {
                $unjustDamagesCheckbox = '<input type="checkbox" checked/>';
            } else {
                $unjustDamagesCheckbox = '<input type="checkbox"/>';
            }

            //Lease Type
            if ($isResidential == true) {
                $isResidential = '<input type="checkbox" checked/>';
                $isNotResidential = '<input type="checkbox"/>';
            } else {
                $isResidential = '<input type="checkbox"/>';
                $isNotResidential = '<input type="checkbox" checked/>';
            }

            //Notice Status
            if ($noQuitNotice == true) {
                $noQuitNotice = '<input type="checkbox" checked/>';
                $quitNoticeGiven = '<input type="checkbox"/>';
            } else {
                $noQuitNotice = '<input type="checkbox"/>';
                $quitNoticeGiven = '<input type="checkbox" checked/>';
            }

            //Lease Status
            if ($unsatisfiedLease == true) {
                $unsatisfiedLease = '<input type="checkbox" checked/>';
            } else {
                $unsatisfiedLease = '<input type="checkbox"/>';
            }
            if ($breachedConditionsLease == true) {
                $breachedConditionsLease = '<input type="checkbox" checked/>';
            } else {
                $breachedConditionsLease = '<input type="checkbox"/>';
            }

            if ($leaseEnded == true) {
                $leaseEnded = '<input type="checkbox" checked/>';
            } else {
                $leaseEnded = '<input type="checkbox"/>';
            }

            //Determination Request
            if ($isDeterminationRequest == true) {
                $determinationRequestCheckbox = '<input type="checkbox" checked/>';
            } else {
                $determinationRequestCheckbox = '<input type="checkbox"/>';
            }

            if ($isAbandoned == true) {
                $abandonedCheckbox = '<input type="checkbox" checked/>';
            } else {
                $abandonedCheckbox = '<input type="checkbox"/>';
            }

            if ($isAdditionalRent == true) {
                $additionalRentCheckbox = '<input type="checkbox" checked/>';
            } else {
                $additionalRentCheckbox = '<input type="checkbox"/>';
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
<span style="position:absolute;left:447.28px;top:180.90px" class="cls_009"><span class="cls_009">DEFENDANT:</span><br><p style="margin-left:6px;">'.$tenantName.'<br>'.$defendantHouseNum.' '.$defendantStreetName.' '. $unitNum . '<br>'.$defendantTown .',' . $defendantState.' '.$defendantZipcode.'  </p></span><br>
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
<span style="position:absolute;left:75.40px;top:454.21px" class="cls_004"><span class="cls_004">______________________________________________________________  in the amount of:</span></span><br>
<span style="position:absolute;left:600.40px;top:454.45px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.40px;top:454.21px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________'.$damageAmt.'_________</span></span><br>
<span style="position:absolute;left:60.50px;top:474.95px" class="cls_004"><span class="cls_004">'. $unjustDamagesCheckbox . 'Damages for the unjust detention of the real property in the amount of</span></span><br>
<span style="position:absolute;left:600.40px;top:474.95px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.42px;top:474.95px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________'.$unjustDamages.'_________</span></span><br>
<span style="position:absolute;left:60.50px;top:494.45px" class="cls_004"><span class="cls_004">'. $amtGreaterThanZeroCheckbox .' Rent remaining due and unpaid on filing date in the amount of</span></span><br>
<span style="position:absolute;left:600.40px;top:494.45px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.40px;top:494.45px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________'.$dueRent.'_________</span></span><br>
<span style="position:absolute;left:60.50px;top:514.95px" class="cls_004"><span class="cls_004">'. $additionalRentCheckbox .' And additional rent remaining due and unpaid on hearing date</span></span><br>
<span style="position:absolute;left:600.40px;top:514.95px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.40px;top:514.95px" class="cls_004"><span class="cls_004">___________________</span></span><br>
<span style="position:absolute;left:60.50px;top:534.45px" class="cls_004"><span class="cls_004">' . $attorneyFeesCheckbox . ' Attorney fees in the amount of</span></span><br>
<span style="position:absolute;left:600.40px;top:534.45px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.40px;top:534.45px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________'.$attorneyFees.'_________</span></span><br>
<span style="position:absolute;left:42.30px;top:567.20px" class="cls_004"><span class="cls_004">THE PLAINTIFF FURTHER ALLEGES THAT:</span></span><br>
<span style="position:absolute;left:570.40px;top:567.20px" class="cls_004"><span class="cls_004">Total:</span></span><br>
<span style="position:absolute;left:600.40px;top:567.20px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.40px;top:567.20px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________'.$totalFees.'_________</span></span><br>
<span style="position:absolute;left:55.40px;top:590.15px" class="cls_004"><span class="cls_004">1. The location and the address, if any, of the real property is:</span></span><br>
<span style="position:absolute;left:393.85px;top:590.15px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">'.$defendantHouseNum.' '.$defendantStreetName . ', ' .$unitNum.', ' . $defendantTown .','.$defendantState.' '.$defendantZipcode . '</span></span><br>
<span style="position:absolute;left:55.40px;top:610.05px" class="cls_004"><span class="cls_004">2. The plaintiff is the landlord of that property.</span></span><br>
<span style="position:absolute;left:55.40px;top:630.55px" class="cls_004"><span class="cls_004">3. The plaintiff leased or rented the property to you or to ___________________________________________under whom you claim</span></span><br>
<span style="position:absolute;left:55.40px;top:650.65px" class="cls_004"><span class="cls_004">4.</span></span><br>
<span style="position:absolute;left:65.60px;top:650.65px" class="cls_004"><span class="cls_004">'.$quitNoticeGiven.'Notice to quit was given in accordance with law, or</span></span><br>
<span style="position:absolute;left:65.60px;top:665.15px" class="cls_004"><span class="cls_004">'.$noQuitNotice.'No notice is required under the terms of the lease.</span></span><br>
<span style="position:absolute;left:55.40px;top:685.45px" class="cls_004"><span class="cls_004">5.</span></span><br>
<span style="position:absolute;left:77.30px;top:685.45px" class="cls_004"><span class="cls_004">'.$leaseEnded.'The term for which the property was leased or rented is fully ended, or</span></span><br>
<span style="position:absolute;left:77.30px;top:700.35px" class="cls_004"><span class="cls_004">'.$breachedConditionsLease.'A forfeiture has resulted by reason of a breach of the conditions of the lease, to wit:</span></span><br>
<span style="position:absolute;left:504.74px;top:700.35px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">'.$breachedDetails.'_____</span></span>
<span style="position:absolute;left:77.30px;top:710.35px" class="cls_004"><span class="cls_004">________________________________________________________________________________________________or,</span></span><br>
<span style="position:absolute;left:77.30px;top:725.15px" class="cls_004"><span class="cls_004">___________________________________________________________________________________________________</span></span><br>
<span style="position:absolute;left:77.30px;top:740.55px" class="cls_004"><span class="cls_004">'.$unsatisfiedLease.'Rent reserved and due has, upon demand, remained unsatisfied.</span></span><br>
<span style="position:absolute;left:55.40px;top:760.15px" class="cls_004"><span class="cls_004">6.</span></span><br>
<span style="position:absolute;left:65.50px;top:760.15px" class="cls_004"><span class="cls_004">You retain the real property and refuse to give up to its possession.</span></span><br>
<span style="position:absolute;left:55.40px;top:780.65px" class="cls_004"><span class="cls_004">I, <span style="text-decoration:underline;"> ' . $pmName . ' </span> verify that the facts set forth in this complaint are</span></span><br>
<span style="position:absolute;left:55.40px;top:795.85px" class="cls_004"><span class="cls_004">true and correct to the best of my knowledge, information and belief. This statement is made subject to the penalties of Section 4904</span></span><br>
<span style="position:absolute;left:55.40px;top:810.05px" class="cls_004"><span class="cls_004">of the Crimes Code (18 PA. C.S. § 4904) relating to unsworn falsification to authorities.</span></span><br>
<span style="position:absolute;left:55.40px;top:820.90px" class="cls_004"><span class="cls_004">I certify this filing complies with the UJS Case Records Public Access Policy.</span></span><br>
<span style="position:absolute;left:560.00px;top:870.80px" class="cls_004"><span class="cls_004">(Signature of Plaintiff)</span></span><br>
<span style="position:absolute;left:60.00px;top:890.40px" class="cls_004"><span class="cls_004">The plaintiff\'s attorney shall file an entry of appearance with the magisterial district court pursuant to Pa . R . C . P . M . D . J . 207.1 </span ></span ><br >
<span style = "position:absolute;left:60.90px;top:905.15px" class="cls_005" ><span class="cls_005" >IF YOU HAVE A DEFENSE to this complaint you may present it at the hearing . IF YOU HAVE A CLAIM against the plaintiff arising out of the occupancy of the premises,</span ></span ><br >
<span style = "position:absolute;left:60.90px;top:915.30px" class="cls_005" ><span class="cls_005" > which is in the magisterial district judge jurisdiction and which you intend to assert at the hearing, YOU MUST FILE it on the complaint form at the office BEFORE THE TIME </span ></span ><br >
<span style = "position:absolute;left:60.90px;top:925.45px" class="cls_005" ><span class="cls_005" > set for the hearing . IF YOU DO NOT APPEAR AT THE HEARING, a judgment for possession and costs, and for damages and rent if claimed, may nevertheless be entered </span ></span ><br >
<span style = "position:absolute;left:60.90px;top:935.60px" class="cls_005" ><span class="cls_005" > against you . A judgment against you for possession may result in your EVICTION from the premises .</span ></span ><br >
<span style = "position:absolute;left:60.90px;top:945.75px" class="cls_007" ><span class="cls_007" >If you are disabled and require a reasonable accommodation to gain access to the Magisterial District Court and its services, please </span ></span ><br >
<span style = "position:absolute;left:60.90px;top:955.35px" class="cls_007" ><span class="cls_007" > contact the Magisterial District Court at the above address or telephone number . We are unable to provide transportation .</span ></span ><br >
<span style = "position:absolute;left:55.40px;top:985.85px" class="cls_008" ><span class="cls_008" > AOPC 310A </span ></span ><br >
<span style = "position:absolute;left:605.75px;top:985.50px" class="cls_008" ><span class="cls_008" > FREE INTERPRETER</span ></span ><br >
<span style = "position:absolute;left:590.75px;top:1000.50px" class="cls_008" ><span class="cls_008" > www.pacourts.us/language-rights</span ></span ><br >
<span style = "position:absolute;left:303.75px;top:985.50px" class="cls_008" ><span class="cls_008" > '.$evictionId .'</span ></span ><br >
<span style = "position:absolute;left:120.65px;top:985.85px" class="cls_007" ><span class="cls_007" > </span >Filing Fee: $'.$filingFee.'</span ><br >
<span style = "position:absolute;left:452.45px;top:1000px" class="cls_010" ><span class="cls_010" > </span ></span >
</span ></body ></html>');

            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->stream();

            return 'success';
        } catch (\Exception $e) {
            $errorDetails = 'DashboardController - error in downloadpdf() method when attempting to download previous eviction';
            $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
            $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();
            \Log::error($errorDetails . PHP_EOL . 'Error Message: ' . $e->getMessage() . PHP_EOL . 'Trace: ' . $e->getTraceAsString());
            mail('andrew.gaidis@gmail.com', 'Showing Dashboard Page', $errorDetails);
            return 'failure';
        }
    }
}
