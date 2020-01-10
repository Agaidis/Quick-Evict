<?php

namespace App\Http\Controllers;

use App\ErrorLog;
use App\FileAddress;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class FileStorageController extends Controller {

    public function __construct()

    {

    }

    public function getFilings () {
        try {
            $filings = FileAddress::where('filing_id', $_POST['id'])->get();
            Log::info($_POST['id']);

            return $filings;

        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }

    public function downloadFilings () {
        //This is where they are already laid out on the page and can be clicked
        try {
            Log::info($_POST['filing_id']);
            $filings = FileAddress::where('file_address', $_POST['filing_original_name'])->first();

            return Response::download(storage_path("app/public/extra_files/" . $filings->file_address));
           // return Storage::disk('public')->download(storage_path("app/public/".$filings->filing_address));

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
