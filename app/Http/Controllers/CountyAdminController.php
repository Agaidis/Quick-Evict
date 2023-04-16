<?php

namespace App\Http\Controllers;

use App\CourtDetails;
use App\ErrorLog;
use App\CountyNotes;
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

    public function getNotes(Request $request) {
        try {
            $updatedCountyNotes = CountyNotes::where('county', $request->county)->orderBy('id', 'DESC')->get();

            return $updatedCountyNotes;
        } catch (Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return view('countyAdmin');
        }
    }

    public function addNote(Request $request) {
        try {

            $userName = Auth()->user()->name;
            $date = date('m/d/Y h:i:s', strtotime('-4 hours'));

            $newCountyNote = new CountyNotes();
            $newCountyNote->county = $request->county;
            $newCountyNote->save();


            CountyNotes::where('id', $newCountyNote->id)
                ->update(['notes' => '<div class="county_note" id="county_'.$newCountyNote->id.'"><p style="font-size:14px; margin-bottom:0;"> '.$userName . ' | '. $date . '<span class="fas fa-trash delete_county_note" id="delete_county_note_'.$newCountyNote->id.'" style="display:none; cursor:pointer; color:red;"></span></p>' . $request->note .'<hr></div>']);

            $currentCountyNotes = CountyNotes::where('county', $request->county)->orderBy('id', 'DESC')->get();

            return $currentCountyNotes;

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
