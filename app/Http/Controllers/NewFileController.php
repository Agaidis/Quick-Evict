<?php

namespace App\Http\Controllers;

use App\GeoLocation;
use Illuminate\Http\Request;
use App\CourtDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use JavaScript;
use GMaps;

class NewFileController extends Controller
{
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
            $counties = CourtDetails::distinct()->get(['county']);

            return view('newFile', compact('counties'));
        }
    }

    public function proceedToFileTypeWithSelectedCounty(Request $request) {
        if (Auth::guest()) {
            return view('/login');
        } else {
            try {
                $geoData = GeoLocation::where('county', $request->county)->orderBy('magistrate_id', 'ASC')->get();
                $map = new GMaps;

                foreach ($geoData as $geo) {
                    $township = CourtDetails::where('magistrate_id', $geo['magistrate_id'])->value('township');
                    $isOnlineAccepted = CourtDetails::where('magistrate_id', $geo['magistrate_id'])->value('online_submission');
                    $geo['township'] = $township;
                    $geo['isOnlineAccepted'] = $isOnlineAccepted;
                }

                JavaScript::put([
                    'geoData' => $geoData,
                    'userId' => Auth::user()->role,
                    'userEmail' => Auth::user()->email
                ]);

                if ($request->fileType == 'ltc') {
                    return view('eviction', compact('map'));
                } else if ($request->fileType == 'oop') {
                    return view('orderOfPossession', compact('map'));
                } else if ($request->fileType == 'civil') {
                    return view('civilComplaint', compact('map'));
                } else {
                    return view('dashboard');
                }
            } catch (Exception $e) {
                $errorDetails = 'NewFileController - error in proceedToFileTypeWithSelectedCounty() method when attempting to navigate user';
                $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
                $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();
                $errorDetails .= PHP_EOL . 'Message ' . $e->getMessage();
                Log::error($errorDetails . PHP_EOL . 'Error Message: ' . $e->getMessage() . PHP_EOL . 'Trace: ' . $e->getTraceAsString());
                mail('andrew.gaidis@gmail.com', 'Proceeding to File Type', $errorDetails);
                return 'failure';
            }
        }
    }
}
