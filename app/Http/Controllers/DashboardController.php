<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Evictions;
use Dompdf\Options;
use App\CourtDetails;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use App\Signature;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Mailgun\Mailgun;

class DashboardController extends Controller
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

        try {

            $userId = Auth::user()->id;
            $courtNumber = Auth::user()->court_id;

            if (Auth::user()->role == 'Administrator') {
                $evictions = DB::table('evictions')->orderBy('id', 'desc')->get();
            } else if (Auth::user()->role == 'General User') {
                $evictions = DB::select('select * from evictions WHERE user_id = '. $userId .' ORDER BY FIELD(status, "Created LTC", "LTC Mailed", "LTC Submitted Online", "Court Hearing Scheduled", "Court Hearing Extended", "Judgement Issued in Favor of Owner", "Judgement Denied by Court", "Tenant Filed Appeal", "OOP Mailed", "OOP Submitted Online", "Paid Judgement", "Locked Out Tenant"), id DESC');
            } else if (Auth::user()->role == 'Court') {
                $evictions = DB::table('evictions')->where('court_number', $courtNumber )->orderBy('id', 'desc')->get();
            } else {
                $evictions = DB::select('select * from evictions ORDER BY FIELD(status, "Created LTC", "LTC Mailed", "LTC Submitted Online", "Court Hearing Scheduled", "Court Hearing Extended", "Judgement Issued in Favor of Owner", "Judgement Denied by Court", "Tenant Filed Appeal", "OOP Mailed", "OOP Submitted Online", "Paid Judgement", "Locked Out Tenant"), id DESC');
            }

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
            Log::error($errorDetails . PHP_EOL . 'Error Message: ' . $e->getMessage() . PHP_EOL . 'Trace: ' . $e->getTraceAsString());
            mail('andrew.gaidis@gmail.com', 'Changing Status', $errorDetails);
        }
    }

    public function storeCourtDate(Request $request) {

        try {
            $courtDateTime = $request->courtDate . ' ' . $request->courtTime;

            $eviction = Evictions::find($request->id);
            $eviction->court_date = $courtDateTime;
            $eviction->save();

            $request->session()->flash('alert-success', 'Date has been successfully set!');

            return 'success';

        } catch (\Exception $e) {
            $errorDetails = 'DashboardController - error in store() method when attempting to store court date';
            $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
            $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();
            Log::error($errorDetails . PHP_EOL . 'Error Message: ' . $e->getMessage() . PHP_EOL . 'Trace: ' . $e->getTraceAsString());
            mail('andrew.gaidis@gmail.com', 'store court date', $errorDetails);
        }
        return 'success';
    }

    public function downloadPDF(Request $request)
    {
        try {
            mail('andrew.gaidis@gmail.com', 'request id', 'this is the request id: ' . $request->id);
            $evictionData = Evictions::where('id', $request->id)->first();
            mail('andrew.gaidis@gmail.com', 'evictionId', 'this is the magistrate id: ' . $evictionData->magistrate_id);


            $courtDetails = CourtDetails::where('magistrate_id', $evictionData->magistrate_id)->first();

            if (Auth::user()->court_id == $evictionData->court_number) {
                $evictionData->is_downloaded = 1;
                $evictionData->save();
            }

            $signature = Signature::where('eviction_id', $evictionData->id)->value('signature');


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
            $breachedDetails = $evictionData->breached_details;
            $propertyDamageDetails = $evictionData->property_damage_details;
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
            $isAbandoned = $evictionData->is_abandoned;
            $isDeterminationRequest = $evictionData->is_determination_request;
            $isAdditionalRent = $evictionData->is_additional_rent;
            $additionalRentAmt = $evictionData->additional_rent_amt;
            $unitNum = $evictionData->unit_num;
            $plantiffName = $evictionData->plantiff_name;
            $plantiffPhone = $evictionData->plantiff_phone;
            $plantiffAddress1 = $evictionData->plantiff_address_line_1;
            $plantiffAddress2 = $evictionData->plantiff_address_line_2;
            $verifyName = $evictionData->verify_name;
            $fileType = $evictionData->file_type;

            // OOP unique
            $judgmentAmount = $evictionData->judgment_amount;
            $costOriginalLTProceeding = $evictionData->costs_original_lt_proceeding;
            $costThisProceeding = $evictionData->cost_this_proceeding;
            $docketNumber = $evictionData->docket_number;

            // Civil Complaint Unique
            $claimDescription = $evictionData->claim_description;


            $defendantAddress = $defendantHouseNum . ' ' .$defendantStreetName . ', ' .$unitNum .' '. $defendantTown .', '.$defendantState.' '.$defendantZipcode;


            //EVICTION CHECK BOXES

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

            if ($fileType == 'eviction' || $fileType == '') {
                $dompdf->loadHtml('<html>
<head><meta http-equiv=Content-Type content="text/html; charset=UTF-8">
<style type="text/css">
<!--
input[type=checkbox] { display: inline!important; font-size: 9pt; margin:1%; }
span.cls_003{font-family:Arial,serif;font-size:13.30px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_002{font-family:Arial,serif;font-size:18.75px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_005{font-family:Arial,serif;font-size:9.31px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_004{font-family:Arial,serif;font-size:12.10px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_006{font-family:Arial,serif;font-size:7.98px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_009{font-family:Arial,serif;font-size:9.31px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_007{font-family:Arial,serif;font-size:10.77px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_008{font-family:Arial,serif;font-size:10.77px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}

--></style></head><body>
<span style="position:absolute;margin-left:-44px;top:-22px;width:787px;height:1112px;overflow:hidden">
<span style="position:absolute;left:0px;top:0px"><img src="https://quickevict.nyc3.digitaloceanspaces.com/background1.jpg" width="800" height="1052"></span>
<span style="position:absolute;left:47.95px;top:16.85px" class="cls_003"><span class="cls_003">COMMONWEALTH OF PENNSYLVANIA</span></span><br>
<span style="position:absolute;left:460.45px;top:16.80px" class="cls_002"><span class="cls_002">LANDLORD/TENANT COMPLAINT</span></span><br>
<span style="position:absolute;left:47.95px;top:29.55px" class="cls_003"><span class="cls_003">COUNTY OF ' . strtoupper($courtDetails->county) . '</span></span><br>
<span style="position:absolute;left:447.28px;top:86.80px" class="cls_005"><span class="cls_005">PLAINTIFF:</span><br><p style="margin-left:6px;">' . $plantiffName . '<br>' . $plantiffAddress1 . '<br>' . $plantiffAddress2 . '<br>' . $plantiffPhone . '</p></span><br>
<span style="position:absolute;left:600.50px;top:86.80px" class="cls_005"><span class="cls_005">NAME and ADDRESS</span></span>
<span style="position:absolute;left:55.40px;top:92.36px" class="cls_004"><span class="cls_004">Mag. Dist. No: ' . $courtNumber . '</span></span><br>
<span style="position:absolute;left:55.40px;top:105.85px" class="cls_004"><span class="cls_004">MDJ Name: ' . $courtDetails->mdj_name . '</span></span><br>
<span style="position:absolute;left:55.40px;top:120.05px" class="cls_004"><span class="cls_004">Address: ' . $courtAddressLine1 . '<br><span style="margin-left:45px;">' . $courtAddressLine2 . '</span></span></span><br>
<span style="position:absolute;left:581.34px;top:183.90px" class="cls_006"><span class="cls_006">V.</span></span><br>
<span style="position:absolute;left:447.28px;top:180.90px" class="cls_009"><span class="cls_009">DEFENDANT:</span><br><p style="margin-left:6px;">' . $tenantName . '<br>' . $defendantHouseNum . ' ' . $defendantStreetName . ' ' . $unitNum . '<br>' . $defendantTown . ', ' . $defendantState . ' ' . $defendantZipcode . '  </p></span><br>
<span style="position:absolute;left:600.50px;top:180.00px" class="cls_005"><span class="cls_005">NAME and ADDRESS</span></span><br>
<span style="position:absolute;left:55.40px;top:188.45px" class="cls_004"><span class="cls_004">Telephone: ' . $courtDetails->phone_number . '</span></span><br>
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
<span style="position:absolute;left:90.87px;top:385.21px" class="cls_004"><span class="cls_004">Lease is</span><span style="margin-left:350px;">' . $monthlyRent . '</span></span><br>
<span style="position:absolute;left:165.25px;top:385.21px" class="cls_004"><span class="cls_004">' . $isResidential . 'Residential</span></span><br>
<span style="position:absolute;left:260.23px;top:385.21px" class="cls_004"><span class="cls_004">' . $isNotResidential . 'Nonresidential     Monthly Rent  $</span></span><br>
<span style="position:absolute;left:550.98px;top:385.21px" class="cls_004"><span class="cls_004">Security Deposit $</span><span style="margin-left:30px;">' . $securityDeposit . '</span></span><br>
<span style="position:absolute;left:55.40px;top:404.91px" class="cls_004"><span class="cls_004">' . $abandonedCheckbox . ' A determination that the manufactured home and property have been abandoned.</span></span><br>
<span style="position:absolute;left:55.40px;top:420.61px" class="cls_004"><span class="cls_004">' . $determinationRequestCheckbox . ' A Request for Determination of Abandonment (Form MDJS 334) must be completed and submitted with this complaint.</span></span><br>
<span style="position:absolute;left:55.40px;top:435.71px" class="cls_004"><span class="cls_004">' . $damageAmtCheckbox . ' Damages for injury to the real property, to wit: ___<span style="text-decoration:underline;">' . $propertyDamageDetails . '</span></span></span><br>
<span style="position:absolute;left:75.40px;top:454.21px" class="cls_004"><span class="cls_004">______________________________________________________________  in the amount of:</span></span><br>
<span style="position:absolute;left:600.40px;top:454.45px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.40px;top:454.21px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________' . $damageAmt . '_________</span></span><br>
<span style="position:absolute;left:60.50px;top:474.95px" class="cls_004"><span class="cls_004">' . $unjustDamagesCheckbox . 'Damages for the unjust detention of the real property in the amount of</span></span><br>
<span style="position:absolute;left:600.40px;top:474.95px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.42px;top:474.95px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________' . $unjustDamages . '_________</span></span><br>
<span style="position:absolute;left:60.50px;top:494.45px" class="cls_004"><span class="cls_004">' . $amtGreaterThanZeroCheckbox . ' Rent remaining due and unpaid on filing date in the amount of</span></span><br>
<span style="position:absolute;left:600.40px;top:494.45px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.40px;top:494.45px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________' . $dueRent . '_________</span></span><br>
<span style="position:absolute;left:60.50px;top:514.95px" class="cls_004"><span class="cls_004">' . $additionalRentCheckbox . ' And additional rent remaining due and unpaid on hearing date</span></span><br>
<span style="position:absolute;left:600.40px;top:514.95px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.40px;top:514.95px" class="cls_004"><span class="cls_004">__________' . $additionalRentAmt . '_________</span></span><br>
<span style="position:absolute;left:60.50px;top:534.45px" class="cls_004"><span class="cls_004">' . $attorneyFeesCheckbox . ' Attorney fees in the amount of</span></span><br>
<span style="position:absolute;left:600.40px;top:534.45px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.40px;top:534.45px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________' . $attorneyFees . '_________</span></span><br>
<span style="position:absolute;left:42.30px;top:567.20px" class="cls_004"><span class="cls_004">THE PLAINTIFF FURTHER ALLEGES THAT:</span></span><br>
<span style="position:absolute;left:570.40px;top:567.20px" class="cls_004"><span class="cls_004">Total:</span></span><br>
<span style="position:absolute;left:600.40px;top:567.20px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:600.40px;top:567.20px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">__________' . $totalFees . '_________</span></span><br>
<span style="position:absolute;left:55.40px;top:590.15px" class="cls_004"><span class="cls_004">1. The location and the address, if any, of the real property is:</span></span><br>
<span style="position:absolute;left:393.85px;top:590.15px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">' . $defendantAddress . '</span></span><br>
<span style="position:absolute;left:55.40px;top:610.05px" class="cls_004"><span class="cls_004">2. The plaintiff is the landlord of that property.</span></span><br>
<span style="position:absolute;left:55.40px;top:630.55px" class="cls_004"><span class="cls_004">3. The plaintiff leased or rented the property to you or to ___________________________________________under whom you claim</span></span><br>
<span style="position:absolute;left:55.40px;top:650.65px" class="cls_004"><span class="cls_004">4.</span></span><br>
<span style="position:absolute;left:65.60px;top:650.65px" class="cls_004"><span class="cls_004">' . $quitNoticeGiven . 'Notice to quit was given in accordance with law, or</span></span><br>
<span style="position:absolute;left:65.60px;top:665.15px" class="cls_004"><span class="cls_004">' . $noQuitNotice . 'No notice is required under the terms of the lease.</span></span><br>
<span style="position:absolute;left:55.40px;top:685.45px" class="cls_004"><span class="cls_004">5.</span></span><br>
<span style="position:absolute;left:77.30px;top:685.45px" class="cls_004"><span class="cls_004">' . $leaseEnded . 'The term for which the property was leased or rented is fully ended, or</span></span><br>
<span style="position:absolute;left:77.30px;top:700.35px" class="cls_004"><span class="cls_004">' . $breachedConditionsLease . 'A forfeiture has resulted by reason of a breach of the conditions of the lease, to wit:</span></span><br>
<span style="position:absolute;left:504.74px;top:700.35px" class="cls_004"></span>
<span style="position:absolute;left:77.30px;top:712.35px" class="cls_004"><span style="text-decoration: underline;" class="cls_004">' . $breachedDetails . '</span> or,</span><br>
<span style="position:absolute;left:77.30px;top:725.15px" class="cls_004"><span class="cls_004">___________________________________________________________________________________________________</span></span><br>
<span style="position:absolute;left:77.30px;top:740.55px" class="cls_004"><span class="cls_004">' . $unsatisfiedLease . 'Rent reserved and due has, upon demand, remained unsatisfied.</span></span><br>
<span style="position:absolute;left:55.40px;top:760.15px" class="cls_004"><span class="cls_004">6.</span></span><br>
<span style="position:absolute;left:65.50px;top:760.15px" class="cls_004"><span class="cls_004">You retain the real property and refuse to give up to its possession.</span></span><br>
<span style="position:absolute;left:55.40px;top:780.65px" class="cls_004"><span class="cls_004">I, <span style="text-decoration:underline;"> ' . $verifyName . ' </span> verify that the facts set forth in this complaint are</span></span><br>
<span style="position:absolute;left:55.40px;top:795.85px" class="cls_004"><span class="cls_004">true and correct to the best of my knowledge, information and belief. This statement is made subject to the penalties of Section 4904</span></span><br>
<span style="position:absolute;left:55.40px;top:810.05px" class="cls_004"><span class="cls_004">of the Crimes Code (18 PA. C.S. § 4904) relating to unsworn falsification to authorities.</span></span><br>
<span style="position:absolute;left:55.40px;top:820.90px" class="cls_004"><span class="cls_004">I certify this filing complies with the UJS Case Records Public Access Policy.</span></span><br>
<span style="position:absolute;left:560.00px;top:870.80px" class="cls_004"><img style="position:absolute; top:-65px" width="160" height="65" src="' . $signature . '"/><span class="cls_004">(Signature of Plaintiff)</span></span><br>
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
<span style = "position:absolute;left:303.75px;top:985.50px" class="cls_008" ><span class="cls_008" > CourtZip ID # ' . $evictionId . '</span ></span ><br >
<span style = "position:absolute;left:120.65px;top:985.85px" class="cls_007" ><span class="cls_007" > </span >Filing Fee: $' . $filingFee . '</span ><br >
</span ></body ></html>');
            } else if ($fileType == 'oop') {
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
<span style="position:absolute;left:450px;top:200px" class="cls_005"><span class="cls_005">DEFENDANT:</span><br><p style="margin-left:65px;">' . $tenantName . '<br>' . $defendantHouseNum . ' ' . $defendantStreetName . ' ' . $unitNum . '<br>' . $defendantTown . ', ' . $defendantState . ' ' . $defendantZipcode . '  </p></span><br>
<span style="position:absolute;left:51px;top:165px" class="cls_004"><span class="cls_004">Address: '.$courtAddressLine1.'<p style="margin-left:49px; margin-top:-4px;">'.$courtAddressLine2.'</p></span></span>
<span style="position:absolute;left:51px;top:205px" class="cls_004"><span class="cls_004">Telephone:</span>'.$courtDetails->phone_number.'</span>
<span style="position:absolute;left:450px;top:310px" class="cls_004"><span class="cls_004">Docket No:</span> '. $docketNumber .'</span>
<span style="position:absolute;left:450px;top:325px" class="cls_004"><span class="cls_004">Case Filed:</span></span>
<span style="position:absolute;left:450px;top:340px" class="cls_004"><span class="cls_004">Time Filed:</span></span>
<span style="position:absolute;left:450px;top:355px" class="cls_004"><span class="cls_004">Date Order Filed:</span></span>
<span style="position:absolute;left:135.00px;top:430px" class="cls_004"><span class="cls_004">Judgment Amount</span></span>
<span style="position:absolute;left:235.00px;top:430px" class="cls_003"><span class="cls_003">$</span>'. $judgmentAmount .'</span>
<span style="position:absolute;left:60.00px;top:445px" class="cls_004"><span class="cls_004">Costs in Original LT Proceeding</span></span>
<span style="position:absolute;left:235.00px;top:445px" class="cls_003"><span class="cls_003">$</span>'. $costOriginalLTProceeding .'</span>
<span style="position:absolute;left:105.00px;top:460px" class="cls_004"><span class="cls_004">Costs in this Proceeding</span></span>
<span style="position:absolute;left:235.00px;top:460px" class="cls_003"><span class="cls_003">$</span>'. $costThisProceeding .'</span>
<span style="position:absolute;left:157px;top:475px" class="cls_004"><span class="cls_004">Attorney Fees</span></span>
<span style="position:absolute;left:235px;top:475px" class="cls_003"><span class="cls_003">$</span>'. $attorneyFees .'</span>
<span style="position:absolute;left:200px;top:490px" class="cls_004"><span class="cls_004">Total</span></span>
<span style="position:absolute;left:235px;top:490px" class="cls_003"><span class="cls_003">$</span>'. $totalFees .'</span>
<span style="position:absolute;left:50px;top:570px" class="cls_004"><span class="cls_004">TO THE MAGISTERIAL DISTRICT JUDGE:</span></span>
<span style="position:absolute;left:50px;top:585px" class="cls_004"><span class="cls_004">The Plaintiff(s) named below, having obtained a judgment for possession of real property located at:</span><br>'.$defendantHouseNum.' '.$defendantStreetName.' '. $unitNum . '<br><br><span style="position:absolute; margin-top:-10px;">'.$defendantTown .', ' . $defendantState.' '.$defendantZipcode.'  </span></span>
<span style="position:absolute;left:50px;top:665px" class="cls_004"><span class="cls_004">Address if any:</span></span>
<span style="position:absolute;left:50px;top:720px" class="cls_004"><span class="cls_004">Requests that you issue an ORDER FOR POSSESSION for such property.</span></span>
<span style="position:absolute;left:50px;top:745px" class="cls_004"><span class="cls_004">I certify that this filing complies with the provisions of the Case Records Public Access Policy of the Unified Judicial</span></span>
<span style="position:absolute;left:50px;top:760px" class="cls_004"><span class="cls_004">System of Pennsylvania that require filing confidential information and documents differently than non-confidential</span></span>
<span style="position:absolute;left:50px;top:775px" class="cls_004"><span class="cls_004">information and documents.</span></span>
<span style="position:absolute;left:50px;top:840px" class="cls_004"><span class="cls_004">Plaintiff:</span> '. $plantiffName .'</span>
<span style="position:absolute;left:427.00px;top:840px" class="cls_004"><span class="cls_004">Date:</span> '. date("m/d/Y") .'</span>
<span style="position:absolute;left:358.00px;top:865px" class="cls_004"><span class="cls_004">Plaintiff Signature:</span><img style="position:absolute; margin-top: -5px; margin-left:10px;" width="160" height="35" src="'.$signature.'"/></span>
<span style="position:absolute;left:55px;top:985px" class="cls_007"><span class="cls_007">AOPC 311A</span></span>
<span style="position:absolute;left:605px;top:985px" class="cls_008"><span class="cls_008">FREE INTERPRETER</span></span>
<span style="position:absolute;left:590px;top:1000px" class="cls_009"><span class="cls_009">www.pacourts.us/language-rights</span></span><br>
<span style = "position:absolute;left:270px;top:985px" class="cls_008" ><span class="cls_008" > CourtZip ID #'.$evictionId.' </span ></span ><br >
<span style = "position:absolute;left:120.65px;top:985.85px" class="cls_007" ><span class="cls_007" > </span >Filing Fee: $' . $filingFee . '</span ><br >
</span></body></html>
');
            } else if ($fileType == 'civil complaint') {
                $dompdf->loadHtml('<html>
<head><meta http-equiv=Content-Type content="text/html; charset=UTF-8">
<style type="text/css">
<!--
span.cls_002{font-family:Arial,serif;font-size:19px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_003{font-family:Arial,serif;font-size:14px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_004{font-family:Arial,serif;font-size:13px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_005{font-family:Arial,serif;font-size:10px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_006{font-family:Arial,serif;font-size:10px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_007{font-family:Arial,serif;font-size:13px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_008{font-family:Arial,serif;font-size:12px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_009{font-family:Arial,serif;font-size:10px;color:rgb(0,0,0);font-weight:bold;font-style:normal;text-decoration: none}
span.cls_010{font-family:Arial,serif;font-size:12px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_011{font-family:Arial,serif;font-size:12px;color:rgb(0,0,0);font-weight:normal;font-style:normal;text-decoration: underline}
--></style></head><body>
<span style="position:absolute;margin-left:-44px;top:-22px;width:787px;height:1112px;overflow:hidden">
<span style="position:absolute;left:0px;top:0px"><img src="https://quickevict.nyc3.digitaloceanspaces.com/civilcomplaint.jpg" width="800" height="1052"></span>
<span style="position:absolute;left:48px;top:16px" class="cls_003"><span class="cls_003">COMMONWEALTH OF PENNSYLVANIA</span></span>
<span style="position:absolute;left:500px;top:16px" class="cls_002"><span class="cls_002">CIVIL COMPLAINT</span></span>
<span style="position:absolute;left:48px;top:35px" class="cls_003"><span class="cls_003">COUNTY OF ' . strtoupper($courtDetails->county) .'</span></span>
<span style="position:absolute;left:450px;top:110px" class="cls_010"><span class="cls_010">PLAINTIFF:</span><br><p style="margin-left:6px;">'. $plantiffName .'<br>'. $plantiffAddress1 .'<br>'. $plantiffAddress2 .'<br>'.$plantiffPhone.'</p></span><br>
<span style="position:absolute;left:615px;top:110px" class="cls_005"><span class="cls_005">NAME and ADDRESS</span></span>
<span style="position:absolute;left:55px;top:120px" class="cls_004"><span class="cls_004">Mag. Dist. No: '. $courtNumber .'</span></span><br>
<span style="position:absolute;left:55px;top:140px" class="cls_004"><span class="cls_004">MDJ Name: '. $courtDetails->mdj_name .'</span></span><br>
<span style="position:absolute;left:55px;top:165px" class="cls_004"><span class="cls_004">Address: '.$courtAddressLine1.'<br><span style="margin-left:50px;">'.$courtAddressLine2.'</span></span></span><br>
<span style="position:absolute;left:581px;top:190px" class="cls_006"><span class="cls_006">V.</span></span>
<span style="position:absolute;left:450px;top:205px" class="cls_010"><span class="cls_010">DEFENDANT:</span><br><p style="margin-left:6px;">'.$tenantName.'<br>'. $defendantState . '<br>'.$defendantZipcode.' </p></span><br>
<span style="position:absolute;left:615px;top:205px" class="cls_005"><span class="cls_005">NAME and ADDRESS</span></span><br>
<span style="position:absolute;left:55px;top:200px" class="cls_004"><span class="cls_004">Telephone: '.$courtDetails->phone_number.'</span></span><br>
<span style="position:absolute;left:190px;top:240px" class="cls_004"><span class="cls_004">AMOUNT</span></span><br>
<span style="position:absolute;left:288px;top:240px" class="cls_004"><span class="cls_004">DATE PAID</span></span><br>
<span style="position:absolute;left:50px;top:256px" class="cls_004"><span class="cls_004">FILING COSTS</span></span><br>
<span style="position:absolute;left:155px;top:256px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:50px;top:275px" class="cls_004"><span class="cls_004">POSTAGE</span></span><br>
<span style="position:absolute;left:155px;top:275px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:50px;top:294px" class="cls_004"><span class="cls_004">SERVICE COSTS</span></span><br>
<span style="position:absolute;left:155px;top:294px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:481px;top:298px" class="cls_004"><span class="cls_004">Docket No:</span></span><br>
<span style="position:absolute;left:50px;top:312px" class="cls_004"><span class="cls_004">CONSTABLE ED.</span></span><br>
<span style="position:absolute;left:155px;top:312px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:481px;top:315px" class="cls_004"><span class="cls_004">Case Filed:</span></span><br>
<span style="position:absolute;left:50px;top:336px" class="cls_004"><span class="cls_004">TOTAL</span></span><br>
<span style="position:absolute;left:155px;top:336px" class="cls_004"><span class="cls_004">$</span></span><br>
<span style="position:absolute;left:55px;top:388px" class="cls_003"><span class="cls_003">Pa.R.C.P.M.D.J. No. 206 sets forth those costs recoverable by the prevailing party.</span></span>
<span style="position:absolute;left:565px;top:410px" class="cls_003">'. $totalFees .'</span>
<span style="position:absolute;left:55px;top:410px" class="cls_003"><span class="cls_003">To The Defendant:  The above named plaintiff(s) asks judgment against you for $__________________ together with costs</span></span>
<span style="position:absolute;left:166px;top:425px" class="cls_003"><span class="cls_003">upon the following claim (Civil fines must include citation of the statute or ordinance violated):</span></span>
<span style="position:absolute;left:60px;top:460px" class="cls_003">' . $claimDescription . ' </span>
<span style="position:absolute;left:140px;top:600px" class="cls_003">'.$plantiffName.'</span>
<span style="position:absolute;left:55px;top:600px" class="cls_003"><span class="cls_003">I, ________________________________ verify that the facts set forth in this complaint are true and correct to the</span></span>
<span style="position:absolute;left:55px;top:615px" class="cls_003"><span class="cls_003">best of my knowledge, information, and belief.  This statement is made subject to the penalties of Section 4904 of the</span></span>
<span style="position:absolute;left:55px;top:645px" class="cls_003"><span class="cls_003">Crimes Code (18 PA. C.S. § 4904) related to unsworn falsification to authorities.</span></span>
<span style="position:absolute;left:55px;top:675px" class="cls_003"><span class="cls_003">I certify that this filing complies with the provisions of the Case Records Public Access Policy of the Unified Judicial System</span></span>
<span style="position:absolute;left:55px;top:690px" class="cls_003"><span class="cls_003">of Pennsylvania that require filing confidential information and documents differently than non-confidential information and</span></span>
<span style="position:absolute;left:55px;top:705px" class="cls_003"><span class="cls_003">documents.</span></span>
<span style="position:absolute;left:465px;top:765px" class="cls_004"><img style="position:absolute; top:-73px; left:40px;" width="150" height="32" src="'.$signature.'"/><span class="cls_004">(Signature of Plaintiff or Authorized Agent)</span></span>
<span style="position:absolute;left:55px;top:800px" class="cls_004"><span class="cls_004">The plaintiff\'s attorney shall file an entry of appearance with the magisterial district court pursuant to Pa.R.C.P.M.D.J. 207.1</span></span>
<span style="position:absolute;left:50px;top:845px" class="cls_007"><span class="cls_007">If you intend to enter a defense to this complaint, you should notify this office immediately at the above telephone number.  You</span></span>
<span style="position:absolute;left:50px;top:860px" class="cls_007"><span class="cls_007">must appear at the hearing and present your defense.  Unless you do, judgment may be entered against you by default.</span></span>
<span style="position:absolute;left:50px;top:890px" class="cls_004"><span class="cls_004">If you have a claim against the plaintiff which is within the magisterial district judge jurisdiction and which you intend to assert at the</span></span>
<span style="position:absolute;left:50px;top:905px" class="cls_004"><span class="cls_004">hearing, you must file it on a complaint form at this office at least five days before the date set for the hearing.</span></span>
<span style="position:absolute;left:50px;top:930px" class="cls_007"><span class="cls_007">If you are disabled and require a reasonable accommodation to gain access to the Magisterial District Court and its services,</span></span>
<span style="position:absolute;left:50px;top:945px" class="cls_007"><span class="cls_007">please contact the Magisterial District Court at the above address or telephone number.  We are unable to provide</span></span>
<span style="position:absolute;left:50px;top:960px" class="cls_007"><span class="cls_007">transportation.</span></span>
<span style="position:absolute;left:50px;top:985px" class="cls_008"><span class="cls_008">AOPC 308A</span></span>
<span style="position:absolute;left:605px;top:985px" class="cls_009"><span class="cls_009">FREE INTERPRETER</span></span>
<span style="position:absolute;left:590px;top:1000px" class="cls_011"><span class="cls_011"> </span><A HREF="http://www.pacourts.us/language-rights/">www.pacourts.us/language-rights</A> </span>
<span style="position:absolute;left:290px;top:985px" class="cls_008" ><span class="cls_008" > CourtZip ID #'.$evictionId.' </span ></span ><br >
</span></body></html>
');
            } else {

            }

            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->stream();

            return 'success';
        } catch (Exception $e) {
            $errorDetails = 'DashboardController - error in downloadpdf() method when attempting to download previous eviction';
            $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
            $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();
            $errorDetails .= PHP_EOL . 'Message ' .  $e->getMessage();
            Log::error($errorDetails . PHP_EOL . 'Error Message: ' . $e->getMessage() . PHP_EOL . 'Trace: ' . $e->getTraceAsString());
            mail('andrew.gaidis@gmail.com', 'Showing Dashboard Page', $errorDetails);
            return 'failure';
        }
    }
}
