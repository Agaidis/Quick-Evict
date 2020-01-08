<?php

namespace App\Http\Controllers;

use App\CourtDetails;
use App\ErrorLog;
use App\Evictions;
use App\FileAddress;
use App\PDF;
use App\Signature;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Illuminate\Http\Request;
use Chumper\Zipper\Facades\Zipper;
use Illuminate\Support\Facades\Auth;

class FileStorageController extends Controller {

    public function __construct()

    {

    }

    public function getFilings () {
        try {

            $filings = FileAddress::where('filing_id', $_POST['id'])->first();
            $pdfEditor = new PDFEditController();
            $evictionData = Evictions::where('id', $_POST['id'])->first();
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

                $pdfHtml = $pdfEditor->globalHtmlAttributes($pdfHtml, $courtDetails, $plaintiffAddress, $defendantAddress, $signature, $evictionData);
                $pdfHtml = $pdfEditor->localLTCAttributes($pdfHtml, $evictionData);
                $pdfHtml = $pdfEditor->addSampleWatermark($pdfHtml, false);

                $dompdf->loadHtml($pdfHtml);
            } else if ($evictionData->file_type == 'oop') {
                $pdfHtml = PDF::where('name', 'oop')->value('html');
                $pdfHtml = $pdfEditor->globalHtmlAttributes($pdfHtml, $courtDetails, $plaintiffAddress, $defendantAddress, $signature, $evictionData);
                $btmPlaintiffName = $evictionData->pm_name . ',<br>' . $evictionData->pm_company_name . ',<br>' . 'On behalf of ' . $evictionData->owner_name . '<br>' . $evictionData->pm_phone;
                $defendantAddress2 = $evictionData->defendant_house_num . ' ' . $evictionData->defendant_street_name .' '. $evictionData->unit_num . '<br><br><span style="position:absolute; margin-top:-10px;">'. $evictionData->defendant_town .', ' . $evictionData->defendant_state .' '.$evictionData->defendant_zipcode;
                $pdfHtml = $pdfEditor->localOOPAttributes($pdfHtml, $evictionData, $defendantAddress2, $btmPlaintiffName);
                $pdfHtml = $pdfEditor->addSampleWatermark($pdfHtml, false);

                $dompdf->loadHtml($pdfHtml);
            } else if ($evictionData->file_type == 'civil complaint') {
                $pdfHtml = PDF::where('name', 'civil')->value('html');
                $pdfHtml = $pdfEditor->globalHtmlAttributes($pdfHtml, $courtDetails, $plaintiffAddress, $civilDefendantAddress, $signature, $evictionData);
                $pdfHtml = $pdfEditor->localCivilAttributes($pdfHtml, $evictionData);
                $pdfHtml = $pdfEditor->addSampleWatermark($pdfHtml, false);


                $dompdf->loadHtml($pdfHtml);
            } else {
                $pdfHtml = '';
            }

            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
          //  $dompdf->stream();

            Zipper::make(public_path('ZippedFiles/'.$filings->file_address))->folder('courtZipped')->addString($evictionData->file_type . '-filing', $dompdf->output())->close();


            return response()->download(public_path('ZippedFiles/'.$filings->file_address));

        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }

    public function storeFile (Request $request) {
        try {
            $originalName = $request->file('file')->getClientOriginalName();
            $zipFileName = 'court-'.rand().'.zip';

            Zipper::make(public_path('ZippedFiles/'.$zipFileName))->folder('courtZipped')->addString($originalName, file_get_contents($request->file('file')))->close();
            $newFiling = new FileAddress();
            $newFiling->file_address = $zipFileName;
            $newFiling->original_file_name = $request->file('file')->getClientOriginalName();
            $newFiling->save();

           return $newFiling->id;
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }
}
