<?php

namespace App\Http\Controllers;

use App\CourtDetails;
use App\ErrorLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CountyAdminController extends Controller
{
    public function index()
    {
        if (Auth::guest()) {
            return view('/login');
        } else {
            try {
                $counties = DB::table('county_settings')->orderBy('county', 'asc')->get();

                return view('countyAdmin', compact('counties'));

            } catch (Exception $e) {
                $errorMsg = new ErrorLog();
                $errorMsg->payload = $e->getMessage() . ' jhjhjLine #: ' . $e->getLine();

                $errorMsg->save();
                return view('countyAdmin');
            }
        }
    }

    public function updateInPersonComplaint(Request $request) {
        try {

            if ($request->isChecked === 'true') {
                DB::table('county_settings')
                    ->where('county', $request->county)
                    ->update(['is_allowed_in_person_complaint' => 1]);
            } else {
                DB::table('county_settings')
                    ->where('county', $request->county)
                    ->update(['is_allowed_in_person_complaint' => 0]);
            }

            $request->session()->flash('alert-success',  $request->county . ' County has been updated!');
            return back();

        } catch (Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return view('countyAdmin');
        }
    }

    public function addNote(Request $request) {
        try {

            $errorMsg = new ErrorLog();
            $errorMsg->payload = 'county: ' . $request->county . ' notes: ' . $request->note;
            $errorMsg->save();

            DB::insert('insert into county_notes (county, notes) values (?, ?)', [$request->county, $request->notes]);

            return 'success';

        } catch (Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return view('countyAdmin');
        }
    }

    public function deleteNote(Request $request) {
        try {

            return back();

        } catch (Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return view('countyAdmin');
        }
    }
}
