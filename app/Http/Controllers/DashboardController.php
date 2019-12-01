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
use App\PDF;
use Exception;

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
            $pdfEditor = new PDFEditController();
            $evictionData = Evictions::where('id', $request->id)->first();
            $courtDetails = CourtDetails::where('magistrate_id', $evictionData->magistrate_id)->first();
            $signature = Signature::where('eviction_id', $evictionData->id)->value('signature');
            $plaintiffAddress = $evictionData->plantiff_name .'<br>'. $evictionData->plantiff_address_line_1 .'<br>'. $evictionData->plantiff_address_line_2 .'<br>'.$evictionData->plantiff_phone;
            $defendantAddress = $evictionData->tenant_name . '<br>' . $evictionData->defendant_house_num . ' ' .$evictionData->defendant_street_name . ', ' . $evictionData->unit_num .' '. $evictionData->defendant_town .', '. $evictionData->defendant_state .' '. $evictionData->defendant_zipcode;

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

                $pdfHtml = $pdfEditor->globalHtmlAttributes($pdfHtml, $courtDetails, $plaintiffAddress, $defendantAddress, $signature, $evictionData);
                $pdfHtml = $pdfEditor->localLTCAttributes($pdfHtml, $evictionData);
                $pdfHtml = $pdfEditor->addSampleWatermark($pdfHtml, false);

                $dompdf->loadHtml($pdfHtml);
            } else if ($evictionData->file_type == 'oop') {
                $pdfHtml = PDF::where('name', 'oop')->value('html');
                $pdfHtml = $pdfEditor->globalHtmlAttributes($pdfHtml, $courtDetails, $plaintiffAddress, $defendantAddress, $signature, $evictionData);
                $defendantAddress2 = $evictionData->defendant_house_num . ' ' . $evictionData->defendant_street_name .' '. $evictionData->unit_num . '<br><br><span style="position:absolute; margin-top:-10px;">'. $evictionData->defendant_town .', ' . $evictionData->defendant_state .' '.$evictionData->defendant_zipcode;
                $pdfHtml = $pdfEditor->localOOPAttributes($pdfHtml, $evictionData, $defendantAddress2);
                $pdfHtml = $pdfEditor->addSampleWatermark($pdfHtml, false);

                $dompdf->loadHtml($pdfHtml);
            } else if ($evictionData->file_type == 'civil complaint') {
                $pdfHtml = PDF::where('name', 'civil')->value('html');
                $pdfHtml = $pdfEditor->globalHtmlAttributes($pdfHtml, $courtDetails, $plaintiffAddress, $defendantAddress, $signature, $evictionData);
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
            $errorDetails = 'DashboardController - error in downloadpdf() method when attempting to download previous eviction';
            $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
            $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();
            $errorDetails .= PHP_EOL . 'Message ' .  $e->getMessage();
            Log::error($errorDetails . PHP_EOL . 'Error Message: ' . $e->getMessage() . PHP_EOL . 'Trace: ' . $e->getTraceAsString());
            mail('andrew.gaidis@gmail.com', 'Showing Dashboard Page', $errorDetails);
            return 'failure';
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
}