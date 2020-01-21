<?php

namespace App\Http\Controllers;

use App\ErrorLog;
use App\GeneralAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class GeneralAdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        if (Auth::guest()) {
            return view('/login');
        } else {

            try {
                $drivingFee = GeneralAdmin::where('name', 'mile_fee')->first();

                return view('generalAdmin', compact('drivingFee'));
            } catch ( \Exception $e ) {
                $errorMsg = new ErrorLog();
                $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

                $errorMsg->save();
            }
        }
    }

    public function updateDrivingFee (Request $request) {

        try {
            DB::table('general_admin')
                ->where('name', 'mile_fee')
                ->update(['value' => $request->driving_distance_fee_rate]);

            $request->session()->flash('alert-success', 'Driving Fee Rate has been Successfully Updated!');

            return back();
        } catch ( \Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();

            $request->session()->flash('alert-danger', 'Something went wrong. Contact Dev Team');
            return back();
        }


    }
}
