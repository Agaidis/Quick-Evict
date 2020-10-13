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
                $errorMsg = new ErrorLog();
                $errorMsg->payload = 'Im in the right if part of it at least';

                $errorMsg->save();
                $options->setIsRemoteEnabled(true);
                $dompdf->setOptions($options);

           //     $pdfHtml = PDF::where('name', 'affidavit')->value('html');

                $pdfHtml = '<html>
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

-->

</style></head><body>
<span style="position:absolute;margin-left:-44px;top:-22px;width:787px;height:1112px;overflow:hidden">
<span style="position:absolute;left:0px;top:0px"><img src="https://quickevict.nyc3.digitaloceanspaces.com/oop.jpg" width="800" height="1052"></span>
<span style="position:absolute;left:48px;top:31px" class="cls_003"><span class="cls_003">COMMONWEALTH OF PENNSYLVANIA</span></span>
<span style="position:absolute;left:500.25px;top:31px" class="cls_002"><span class="cls_002">REQUEST FOR ORDER FOR</span></span>
<span style="position:absolute;left:47.95px;top:45px" class="cls_003"><span class="cls_003">COUNTY OF  __str-upper-county__</span></span><br>
<span style="position:absolute;left:570.80px;top:55px" class="cls_002"><span class="cls_002">POSSESSION</span></span>
<span style="position:absolute;left:51px;top:120px" class="cls_004"><span class="cls_004">Mag. Dist. No: __court-number__</span></span>
<span style="position:absolute;left:51px;top:134px" class="cls_004"><span class="cls_004">MDJ Name: __mdj-name__</span></span>
<span style="position:absolute;left:445px;top:100px" class="cls_005"><span class="cls_005">PLAINTIFF:</span><p style="margin-left:65px;">__plaintiff-address__</p></span>
<span style="position:absolute;left:450px;top:185px" class="cls_005"><span class="cls_005">V.</span></span>
<span style="position:absolute;left:450px;top:200px" class="cls_005"><span class="cls_005">DEFENDANT:</span><br><p style="margin-left:65px;">__defendant-address__</p></span><br>
<span style="position:absolute;left:51px;top:165px" class="cls_004"><span class="cls_004">Address: __court-address-one__<p style="margin-left:49px; margin-top:-4px;">__court-address-two__</p></span></span>
<span style="position:absolute;left:51px;top:205px" class="cls_004"><span class="cls_004">Telephone:</span>__phone-number__</span><span style="position:absolute;left:100px;top:280px; color:#f2f2f2; font-size:120px;">PREVIEW</span>
<span style="position:absolute;left:450px;top:310px" class="cls_004"><span class="cls_004">Docket No:</span> __docket-number__</span>
<span style="position:absolute;left:450px;top:325px" class="cls_004"><span class="cls_004">Case Filed:</span></span>
<span style="position:absolute;left:450px;top:340px" class="cls_004"><span class="cls_004">Time Filed:</span></span>
<span style="position:absolute;left:450px;top:355px" class="cls_004"><span class="cls_004">Date Order Filed:</span></span>
<span style="position:absolute;left:135.00px;top:430px" class="cls_004"><span class="cls_004">Judgment Amount</span></span>
<span style="position:absolute;left:235.00px;top:430px" class="cls_003"><span class="cls_003">$</span>__judgment-amount__</span>
<span style="position:absolute;left:60.00px;top:445px" class="cls_004"><span class="cls_004">Costs in Original LT Proceeding</span></span>
<span style="position:absolute;left:235.00px;top:445px" class="cls_003"><span class="cls_003">$</span>__cost-original-lt-proceeding__</span>
<span style="position:absolute;left:105.00px;top:460px" class="cls_004"><span class="cls_004">Costs in this Proceeding</span></span>
<span style="position:absolute;left:235.00px;top:460px" class="cls_003"><span class="cls_003">$</span>__cost-this-proceeding__</span>
<span style="position:absolute;left:157px;top:475px" class="cls_004"><span class="cls_004">Attorney Fees</span></span>
<span style="position:absolute;left:235px;top:475px" class="cls_003"><span class="cls_003">$</span>__attorney-fees__</span>
<span style="position:absolute;left:200px;top:490px" class="cls_004"><span class="cls_004">Total</span></span>
<span style="position:absolute;left:235px;top:490px" class="cls_003"><span class="cls_003">$</span>__total-fees__</span>
<span style="position:absolute;left:50px;top:570px" class="cls_004"><span class="cls_004">TO THE MAGISTERIAL DISTRICT JUDGE:</span></span>
<span style="position:absolute;left:50px;top:585px" class="cls_004"><span class="cls_004">The Plaintiff(s) named below, having obtained a judgment for possession of real property located at:</span><br>__defendant-address-2__</span></span>
<span style="position:absolute;left:50px;top:665px" class="cls_004"><span class="cls_004">Address if any:</span></span>
<span style="position:absolute;left:50px;top:720px" class="cls_004"><span class="cls_004">Requests that you issue an ORDER FOR POSSESSION for such property.</span></span>
<span style="position:absolute;left:50px;top:745px" class="cls_004"><span class="cls_004">I certify that this filing complies with the provisions of the Case Records Public Access Policy of the Unified Judicial</span></span>
<span style="position:absolute;left:50px;top:760px" class="cls_004"><span class="cls_004">System of Pennsylvania that require filing confidential information and documents differently than non-confidential</span></span>
<span style="position:absolute;left:50px;top:775px" class="cls_004"><span class="cls_004">information and documents.</span></span>
<span style="position:absolute;left:50px;top:840px" class="cls_004"><span class="cls_004">Plaintiff:</span> __btm-plaintiff-name__</span>
<span style="position:absolute;left:427.00px;top:840px" class="cls_004"><span class="cls_004">Date:</span> __date__</span>
<span style="position:absolute;left:358.00px;top:865px" class="cls_004"><span class="cls_004">Plaintiff Signature:</span><img style="position:absolute; margin-top: -5px; margin-left:10px;" width="160" height="31" src="__signature__"/></span>
<span style="position:absolute;left:55px;top:985px" class="cls_007"><span class="cls_007">AOPC 311A</span></span>
<span style="position:absolute;left:605px;top:985px" class="cls_008"><span class="cls_008">FREE INTERPRETER</span></span>
<span style="position:absolute;left:590px;top:1000px" class="cls_009"><span class="cls_009">www.pacourts.us/language-rights</span></span><br>
<span style = "position:absolute;left:270px;top:985px" class="cls_008" ><span class="cls_008" > CourtZip ID #__eviction-id__</span ></span ><br >
<span style = "position:absolute;left:120.65px;top:985.85px" class="cls_007" ><span class="cls_007" > </span >Filing Fee: $__filing-fee__</span ><br >
</span></body></html>';

                $errorMsg = new ErrorLog();
                $errorMsg->payload = $pdfHtml;

                $errorMsg->save();
                $civilFiling = CivilRelief::where('id', $_POST['main_filing_id'])->first();

                $errorMsg = new ErrorLog();
                $errorMsg->payload = serialize($civilFiling);

                $errorMsg->save();
                $pdfHtml = $pdfEditor->createCivilReliefActPDF($pdfHtml, $civilFiling);




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
