<?php

namespace App\Http\Controllers;

use App\CourtDetails;
use App\ErrorLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeDuplicatorController extends Controller
{
    public function index()
    {
        if (Auth::guest()) {
            return view('/login');
        } else {
            try {
                $courts = CourtDetails::distinct()->orderBy('court_number')->get(['court_number']);

                return view('feeDuplicator', compact('courts'));

            } catch (Exception $e) {
                $errorMsg = new ErrorLog();
                $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

                $errorMsg->save();
                return view('feeDuplicator');
            }
        }
    }

    public function getSelectedCourtMagistrates(Request $request) {
        try {
            $magistrates = CourtDetails::where('court_number', $request->courtNumber)->get();

            return $magistrates;

        } catch (Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return view('feeDuplicator');
        }
    }

    public function duplicateFees(Request $request) {
        try {

            return back();

        } catch (Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return view('feeDuplicator');
        }
    }
}
