<?php

namespace App\Http\Controllers;

use App\CourtDetails;
use Illuminate\Http\Request;

class MagistrateController extends Controller
{
    public function index() {
        // Get all the series
        $courtDetails = CourtDetails::all();

        return view('magistrateCreator')->with('courtDetails', $courtDetails);
    }

    public function store(Request $request) {
        try {
                $courtDetails = new CourtDetails;
                $courtDetails->county = $request->county;
                $courtDetails->court_number = $request->court_id;
                $courtDetails->one_defendant_up_to_2000 = $request->one_under_2000;
                $courtDetails->two_defendant_up_to_2000 = $request->two_under_2000;
                $courtDetails->one_defendant_between_2001_4000 = $request->one_btn_2000_4001;
                $courtDetails->two_defendant_between_2001_4000 = $request->two_btn_2000_4001;
                $courtDetails->one_defendant_greater_than_4000 = $request->one_over_4000;
                $courtDetails->two_defendant_greater_than_4000 = $request->two_over_4000;
                $courtDetails->one_defendant_out_of_pocket = $request->one_oop;
                $courtDetails->two_defendant_out_of_pocket = $request->two_oop;
                $courtDetails->mailing_address = $request->court_address;
                $courtDetails->mdj_name = $request->mdj_name;
                $courtDetails->phone_number = $request->court_number;
                $courtDetails->save();

            $request->session()->flash('alert-success', 'Magistrate Successfully Added!');
        } catch ( \Exception $e ) {
            $errorDetails = 'MagistrateController - error in store() method when attempting to store magistrate';
            $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
            $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();
            \Log::error( $errorDetails . PHP_EOL . 'Error Message: ' . $e->getMessage() . PHP_EOL . 'Trace: ' . $e->getTraceAsString());
            mail( 'agaidis@moneymappress.com',  'Adding Magistrate Error', $errorDetails );

            $returnArray['responseMessage'] = 'Bad Request';
            $returnArray['responseCode'] = 400;
            $returnArray['messageDetails'] = '' . $e->getMessage() . 'Tag could not be added to the database, please try again later';
            return response()->json($returnArray);
        }
        $response['responseMessage'] = 'Adding Magistrate Successful!';
        $response['messageDetails'] = 'All Good';

        return $response;
    }

    public function delete(Request $request) {
        try {
            mail('andrew.gaidis@gmail.com', 'asdf', $request->id);
            CourtDetails::destroy($request->id);
            return $request->id;
        } catch (\Exception $e) {
            return 'failed';
        }
    }
}
