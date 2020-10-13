<?php

namespace App\Http\Controllers;

use App\CivilRelief;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use stdClass;

class FileStorageController extends Controller {

    public function __construct()

    {

    }

    public function getFilings () {
        try {
            $mainFiling = Evictions::where('id', $_POST['id'])->first();
            $filings = FileAddress::where('filing_id', $_POST['id'])->get();
            $civilReliefFilings = CivilRelief::where('filing_id', $_POST['id'])->get();

            return array(
                'filings' => $filings,
                'mainFiling' => $mainFiling,
                'civilReliefFilings' => $civilReliefFilings
            );

        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }

    public function downloadFilings () {
        //This is where they are already laid out on the page and can be clicked
        try {
            $pdfEditor = new PDFEditController();
            $dompdf = new Dompdf();
            $options = new Options();

            if ($_POST['file_type'] === 'main') {
                $evictionData = Evictions::where('id', $_POST['main_filing_id'])->first();
                $courtDetails = CourtDetails::where('magistrate_id', $evictionData->magistrate_id)->first();
                $signature = Signature::where('eviction_id', $evictionData->id)->value('signature');
                $plaintiffAddress = $evictionData->plantiff_name .'<br>'. $evictionData->plantiff_address_line_1 .'<br>'. $evictionData->plantiff_address_line_2 .'<br>'.$evictionData->plantiff_phone;
                $defendantAddress = $evictionData->tenant_name . '<br>' . $evictionData->defendant_house_num . ' ' .$evictionData->defendant_street_name . ', ' . $evictionData->unit_num .'<br>'. $evictionData->defendant_town .', '. $evictionData->defendant_state .' '. $evictionData->defendant_zipcode;
                $civilDefendantAddress = $evictionData->tenant_name . '<br>' . $evictionData->defendant_state  . ', ' . $evictionData->unit_num .'<br>'.$evictionData->defendant_zipcode;



                $options->setIsRemoteEnabled(true);
                $dompdf->setOptions($options);

                if (Auth::user()->court_id == $evictionData->court_number) {
                    $evictionData->is_downloaded = 1;
                    $evictionData->save();
                }

                if ($evictionData->file_type == 'eviction' || $evictionData->file_type == '') {
                    $pdfHtml = PDF::where('name', 'ltc')->value('html');

                    if ($evictionData->is_resided == 'yes' || $evictionData->is_resided == null) {
                        $defendantAddress2 = $defendantAddress;
                    } else {
                        $defendantAddress2 = str_replace('-1', '<br>', $evictionData->resided_address);
                    }

                    $pdfHtml = $pdfEditor->globalHtmlAttributes($pdfHtml, $courtDetails, $plaintiffAddress, $defendantAddress, $signature, $evictionData);
                    $pdfHtml = $pdfEditor->localLTCAttributes($pdfHtml, $evictionData, $defendantAddress2);
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
                }

                // (Optional) Setup the paper size and orientation
                $dompdf->setPaper('A4', 'portrait');

                // Render the HTML as PDF
                $dompdf->render();

                // Output the generated PDF to Browser
                $dompdf->stream();

                return 'success';

            } else if ($_POST['file_type'] === 'civil') {
                $pdfHtml = PDF::where('name', 'affidavit')->value('html');
                $evictionData = Evictions::where('id', $_POST['main_filing_id'])->first();

                $pdfHtml = $pdfEditor->createCivilReliefActPDF($pdfHtml, $evictionData->id);


                $options->setIsRemoteEnabled(true);
                $dompdf->setOptions($options);

                $dompdf->loadHtml($pdfHtml);

                // (Optional) Setup the paper size and orientation
                $dompdf->setPaper('A4', 'portrait');

                // Render the HTML as PDF
                $dompdf->render();

                // Output the generated PDF to Browser
                $dompdf->stream();

                return 'success';


            } else {
                $filings = FileAddress::where('file_address', $_POST['filing_original_name'])->first();

                return Response::download(storage_path("app/public/extra_files/" . $filings->file_address));
            }
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }

    public function storeFile (Request $request) {
        try {
            $md5Name = md5_file($request->file('file')->getRealPath());
            $guessExtension = $request->file('file')->guessExtension();
            $fileAddress = $request->file('file')->getClientOriginalName() .'-' . rand() . '-' .$md5Name.'.'.$guessExtension;
            $fileAddress = str_replace('_', ' ', $fileAddress);
            $request->file('file')->storeAs('extra_files', $fileAddress );

            $newFiling = new FileAddress();
            $newFiling->file_address = $fileAddress;
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
