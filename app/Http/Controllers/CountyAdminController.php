<?php

namespace App\Http\Controllers;

use App\CourtDetails;
use App\ErrorLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CountyAdminController extends Controller
{
    public function index()
    {
        if (Auth::guest()) {
            return view('/login');
        } else {
            try {
                $counties = CourtDetails::distinct()->orderBy('county', 'asc')->get(['county']);

                return view('countyAdmin', compact('counties'));

            } catch (Exception $e) {
                $errorMsg = new ErrorLog();
                $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

                $errorMsg->save();
                return view('countyAdmin');
            }
        }
    }

    public function updateInPersonComplaint(Request $request) {
        try {

            $request->session()->flash('alert-success',  $request->county . ' County has been updated!');
            return 'success';

        } catch (Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return view('countyAdmin');
        }
    }
}
