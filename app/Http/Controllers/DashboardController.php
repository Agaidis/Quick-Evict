<?php

namespace App\Http\Controllers;

use App\Classes\Mailer;
use App\CountyNotes;
use App\CourtNotification;
use App\ErrorLog;
use App\User;
use Illuminate\Http\Request;
use App\Evictions;
use Dompdf\Options;
use App\CourtDetails;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use App\Signature;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\PDF;
use Exception;
use App\EvictionNote;

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
            $userRole = Auth::user()->role;
            $counties = CourtDetails::distinct()->orderBy('county')->get(['county']);
            $notesArray = array();

            if (Auth::user()->role == 'Administrator') {
                $notes = DB::select('select court_id from county_notes');
                foreach ($notes as $note) {
                    array_push($notesArray, $note->court_id);
                }

                $evictions = DB::table('evictions')
                    ->select('evictions.id', 'users.name AS name', 'user_id', 'property_address', 'status', 'file_type', 'is_downloaded', 'owner_name', 'tenant_name', 'court_date', 'total_judgement', 'filing_fee',  'evictions.created_at', 'is_extra_files', 'court_number', 'is_in_person_filing')
                    ->join('users', 'evictions.user_id', '=', 'users.id')
                    ->orderBy('evictions.id', 'desc')
                    ->take(600)
                    ->get();
            } else if (Auth::user()->role == 'PM Company Leader') {
                $emailAddress = Auth::user()->email;
                $splitEmailAddress = explode('@', $emailAddress);
                $emailDomain = $splitEmailAddress[1];
                $userIdArr = [];

                $pmUseIds = DB::table('users')
                    ->select('users.id')
                    ->where('email','LIKE','%'.$emailDomain.'%')
                    ->get();

                foreach ($pmUseIds as $userData) {
                    array_push($userIdArr, $userData->id);
                }

                $evictions = DB::table('evictions')
                    ->select('evictions.id', 'users.name AS name', 'user_id', 'property_address', 'status', 'file_type', 'is_downloaded', 'owner_name', 'tenant_name', 'court_date', 'total_judgement', 'filing_fee',  'evictions.created_at', 'is_extra_files', 'court_number', 'is_in_person_filing')
                    ->whereIn('user_id', $userIdArr)
                    ->join('users', 'evictions.user_id', '=', 'users.id')
                    ->orderBy('evictions.id', 'desc')
                    ->take(600)
                    ->get();

            } else if (Auth::user()->role == 'General User') {
                $evictions = DB::select('select id, property_address, status, file_type, is_downloaded, owner_name, tenant_name, court_date, total_judgement, filing_fee,  created_at, is_extra_files, court_number, is_in_person_filing from evictions WHERE user_id = '. $userId .' ORDER BY FIELD(status, "Created LTC", "LTC Mailed", "LTC Submitted Online", "Court Hearing Scheduled", "Court Hearing Extended", "Judgement Issued in Favor of Owner", "Judgement Denied by Court", "Tenant Filed Appeal", "OOP Mailed", "OOP Submitted Online", "Paid Judgement", "Locked Out Tenant"), id DESC');
            } else if (Auth::user()->role == 'Court') {

                $evictions = DB::table('evictions')
                    ->select('id', 'property_address', 'status', 'file_type', 'is_downloaded', 'owner_name', 'tenant_name', 'court_date', 'total_judgement', 'filing_fee',  'created_at', 'is_extra_files', 'court_number', 'is_in_person_filing')
                    ->where('court_number', $courtNumber )
                    ->where('is_online_filing', 1)
                    ->orderBy('id', 'desc')
                    ->take(600)
                    ->get();

            } else {
                $evictions = DB::select('select id, property_address, status, file_type, is_downloaded, owner_name, tenant_name, court_date, total_judgement, filing_fee,  created_at, is_extra_files, court_number, is_in_person_filing from evictions ORDER BY FIELD(status, "Created LTC", "LTC Mailed", "LTC Submitted Online", "Court Hearing Scheduled", "Court Hearing Extended", "Judgement Issued in Favor of Owner", "Judgement Denied by Court", "Tenant Filed Appeal", "OOP Mailed", "OOP Submitted Online", "Paid Judgement", "Locked Out Tenant"), id DESC');
            }

            return view('dashboard' , compact('evictions', 'userRole', 'counties', 'notesArray'));
        } catch (\Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
            $errorMsg->save();
        }

    }

    public function navToSignup() {
        try {
            return view('register' );

        } catch(\Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }

    public function statusChange(Request $request) {
        try {
            $eviction = Evictions::find($request->id);
            $eviction->status = $request->status;

            $eviction->save();

            return 'success';
        } catch (\Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }

    public function storeCourtDate(Request $request) {

        try {
            $courtDateTime = $request->courtDate . ' ' . $request->courtTime;

            $eviction = Evictions::find($request->id);

            $eviction->court_date = $courtDateTime;

            $eviction->save();

            $userEmail = User::find($eviction->user_id);

            $courtDate = $courtDateTime;
            $timestamp = strtotime($courtDate); //convert to Unix timestamp
            $dbCourtDate = date("Y-m-d H:i:s", $timestamp );

            $errorMsg = new ErrorLog();
            $errorMsg->payload = 'testing date storage. ' . $userEmail . ' ' . $dbCourtDate;

            $errorMsg->save();

            $propertyAddress = str_replace('-1', ' ', $eviction->property_address);
            $propertyAddress = str_replace(',', ', ', $propertyAddress);


            $emailCourtDate = explode(' ', $dbCourtDate);
            $mailer = new Mailer();
            $mailer->sendMail($userEmail->email, 'CourtZip Filing Update: Court Date and Time Created','Hello,
The court date and time for your Landlord-Tenant magistrate hearing for property ' . $propertyAddress . ' has been set for '. date("F j", $timestamp ) .' at 
' . date("h:i", $timestamp ) . '.. You can always check all upcoming court dates and statuses in your dashboard at CourtZip.com. If you have any questions, 
please reach out to CourtZip at info@CourtZip.com.

Thanks,

CourtZip Team', '<p>Hello,</p>
<p>The court date and time for your Landlord-Tenant magistrate hearing for property ' . $propertyAddress . ' has been set for '. date("F j", $timestamp ) .'  at</p>
<p>' . date("h:i", $timestamp ) . '. You can always check all upcoming court dates and statuses in your dashboard at CourtZip.com. If you have any questions,</p>
<p>please reach out to CourtZip at info@CourtZip.com.</p>

<p>Thanks,</p>

<p>CourtZip Team</p>');

            CourtNotification::updateOrCreate(
                [
                    'eviction_id' => $request->id
                ],
                [
                    'court_number' => $eviction->court_number,
                    'court_date' => $dbCourtDate,
                    'user_id' => $eviction->user_id
                ]
            );

            $request->session()->flash('alert-success', 'Date has been successfully set!');

            return 'success';

        } catch (\Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
        return 'success';
    }

    public function downloadPDF(Request $request)
    {
        try {
            $pdfEditor = new PDFEditController();
            $evictionData = Evictions::where('id', $request->id)->first();
            $courtDetails = CourtDetails::where('magistrate_id', $evictionData->magistrate_id)->first();
            $signature = Signature::where('eviction_id', $evictionData->id)->value('signature');
            $plaintiffAddress = $evictionData->plantiff_name .'<br>'. $evictionData->plantiff_address_line_1 .'<br>'. $evictionData->plantiff_address_line_2 .'<br>'.$evictionData->plantiff_phone;
            $defendantAddress = $evictionData->tenant_name . '<br>' . $evictionData->defendant_house_num . ' ' .$evictionData->defendant_street_name . ', ' . $evictionData->unit_num .'<br>'. $evictionData->defendant_town .', '. $evictionData->defendant_state .' '. $evictionData->defendant_zipcode;

            $civilDefendantAddress = $evictionData->tenant_name . '<br>' . $evictionData->defendant_state  . ', ' . $evictionData->unit_num .'<br>'.$evictionData->defendant_zipcode;

            $dompdf = new Dompdf();
            $options = new Options();

            $options->setIsRemoteEnabled(true);
            $dompdf->setOptions($options);

            if (Auth::user()->court_id == $evictionData->court_number) {
                $evictionData->is_downloaded = 1;
                $evictionData->save();
            }

            if ($evictionData->file_type == 'eviction' || $evictionData->file_type == '') {
                $pdfHtml = PDF::where('name', 'ltc')->value('html');

                if ($evictionData->is_resided == 'yes' || $evictionData->is_resided == null) {
                    $defendantAddress2 = $evictionData->defendant_house_num . ' ' .$evictionData->defendant_street_name . ', ' . $evictionData->unit_num . ' ' . $evictionData->defendant_town .', '. $evictionData->defendant_state .' '. $evictionData->defendant_zipcode;

                } else {
                    $defendantAddress2 = str_replace('-1', '<br>', $evictionData->resided_address);
                }



                $pdfHtml = $pdfEditor->globalHtmlAttributes($pdfHtml, $courtDetails, $plaintiffAddress, $defendantAddress, $signature, $evictionData, $evictionData->is_in_person_filing);
                $pdfHtml = $pdfEditor->localLTCAttributes($pdfHtml, $evictionData, $defendantAddress2);
                $pdfHtml = $pdfEditor->addSampleWatermark($pdfHtml, false);

                $dompdf->loadHtml($pdfHtml);
            } else if ($evictionData->file_type == 'oop') {
                $pdfHtml = PDF::where('name', 'oop')->value('html');
                $pdfHtml = $pdfEditor->globalHtmlAttributes($pdfHtml, $courtDetails, $plaintiffAddress, $defendantAddress, $signature, $evictionData, $evictionData->is_in_person_filing);
                $btmPlaintiffName = $evictionData->pm_name . ',<br>' . $evictionData->pm_company_name . ',<br>' . 'On behalf of ' . $evictionData->owner_name . '<br>' . $evictionData->pm_phone;
                $defendantAddress2 = $evictionData->defendant_house_num . ' ' . $evictionData->defendant_street_name .' '. $evictionData->unit_num . '<br><br><span style="position:absolute; margin-top:-10px;">'. $evictionData->defendant_town .', ' . $evictionData->defendant_state .' '.$evictionData->defendant_zipcode;
                $pdfHtml = $pdfEditor->localOOPAttributes($pdfHtml, $evictionData, $defendantAddress2, $btmPlaintiffName);
                $pdfHtml = $pdfEditor->addSampleWatermark($pdfHtml, false);

                $dompdf->loadHtml($pdfHtml);
            } else if ($evictionData->file_type == 'civil complaint') {
                $pdfHtml = PDF::where('name', 'civil')->value('html');
                $pdfHtml = $pdfEditor->globalHtmlAttributes($pdfHtml, $courtDetails, $plaintiffAddress, $civilDefendantAddress, $signature, $evictionData, $evictionData->is_in_person_filing);
                $pdfHtml = $pdfEditor->localCivilAttributes($pdfHtml, $evictionData);
                $pdfHtml = $pdfEditor->addSampleWatermark($pdfHtml, false);


                $dompdf->loadHtml($pdfHtml);
            }

            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->stream();

            return 'success';
        } catch (Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }


    public function getFileData () {
        try {
            $fileData = Evictions::where('id', $_GET['id'])->first();

            return $fileData;
        } catch ( Exception $e ) {
            mail('andrew.gaidis@gmail.com', 'Error Getting File to Edit', $e->getMessage());
            Log::info($e->getMessage());
            return false;
        }
    }

    public function getCountySettings(Request $request) {
        try {
            $isEnabled = DB::table('county_settings')->where('county', $request->county)->value('is_allowed_in_person_complaint');

            return $isEnabled;
        } catch ( Exception $e ) {
            mail('andrew.gaidis@gmail.com', 'Error Getting File to Edit', $e->getMessage());
            Log::info($e->getMessage());
            return false;
        }
    }

    public function getFiles () {
        try {

        } catch ( Exception $e ) {
            mail('andrew.gaidis@gmail.com', 'Error Getting File to Edit', $e->getMessage());
            Log::info($e->getMessage());
            return false;
        }
    }

    public function editLTC () {
        try {

        } catch ( Exception $e ) {
            mail('andrew.gaidis@gmail.com', 'Error editing LTC', $e->getMessage());
            Log::info($e->getMessage());
        }
    }

    public function editOOP () {
        try {

        } catch ( Exception $e ) {
            mail('andrew.gaidis@gmail.com', 'Error editing OOP', $e->getMessage());
            Log::info($e->getMessage());
        }

    }

    public function editCivil () {
        try {

        } catch ( Exception $e ) {
            mail('andrew.gaidis@gmail.com', 'Error editing CIVIL', $e->getMessage());
            Log::info($e->getMessage());
        }
    }

    public function getEvictionNotes(Request $request) {
        try {

            $updatedEvictionNotes = EvictionNote::where('eviction_id', $request->eviction_id)->orderBy('id', 'DESC')->get();

            $docketNumber = Evictions::where('id', $request->eviction_id)->value('docket_number');

            if ($docketNumber !== '' && $docketNumber !== null) {
                $docketArr = explode('-', $docketNumber);
                $d1 = $docketArr[1];
                $d2 = $docketArr[3];
                $d3 = $docketArr[4];

                $updatedEvictionNotes[0]->d1 = $d1;
                $updatedEvictionNotes[0]->d2 = $d2;
                $updatedEvictionNotes[0]->d3 = $d3;
            }


            return $updatedEvictionNotes;
        } catch (Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
            $errorMsg->save();

            return view('dashboard');
        }
    }

    public function addEvictionNote(Request $request) {
        try {

            $userName = Auth()->user()->name;
            $date = date('m/d/Y h:i:s', strtotime('-4 hours'));

            $newEvictionNote = new EvictionNote();
            $newEvictionNote->eviction_id = $request->eviction_id;
            $newEvictionNote->save();


            EvictionNote::where('id', $newEvictionNote->id)
                ->update(['note' => '<div class="eviction_note" id="eviction_note_'.$newEvictionNote->id.'_'.$request->eviction_id.'"><p style="font-size:14px; margin-bottom:0;"> '.$userName . ' | '. $date . '<span class="fas fa-trash delete_eviction_note" id="delete_eviction_note_'.$newEvictionNote->id.'_'.$request->eviction_id.'" style="display:none; cursor:pointer; color:red; float:right; margin:10px"></span></p>' . $request->note .'<hr></div>']);

            $currentEvictionNotes = EvictionNote::where('eviction_id', $request->eviction_id)->orderBy('id', 'DESC')->get();

            return $currentEvictionNotes;

        } catch (Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return view('dashboard');
        }
    }

    public function deleteEvictionNote(Request $request) {
        try {

            EvictionNote::destroy($request->id);

            $updatedEvictionNotes = EvictionNote::where('eviction_id', $request->evictionId)->orderBy('id', 'DESC')->get();

            return $updatedEvictionNotes;

        } catch (Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return view('dashboard');
        }
    }

    public function updateDocketNumbers(Request $request) {
        try {

            $docketNumber2 = $request->docket_number_2;

            while (strlen($docketNumber2) < 7) {
                $docketNumber2 = '0' . $docketNumber2;
            }
            $docket_number = 'MJ-' . $request->docket_number_1 . '-LT-' . $docketNumber2 . '-' . $request->docket_number_3;

            Evictions::where('id', $request->id)
                ->update(['docket_number' => $docket_number]);

            return 'success';

        } catch (Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return view('dashboard');
        }
    }
}