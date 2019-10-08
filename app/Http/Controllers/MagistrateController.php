<?php

namespace App\Http\Controllers;

use App\CourtDetails;
use App\GeoLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class MagistrateController extends Controller
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
            // Get all the series
            $courtDetails = CourtDetails::all();
            $geoLocations = GeoLocation::all();

            return view('magistrateCreator', compact('courtDetails', 'geoLocations'));
        }
    }

    public function store(Request $request) {
        Log::info('Storing a Magistrate');
        Log::info(Auth::User()->id);
        Log::info($request->online_submission);
        try {
            $isUnique = CourtDetails::where('magistrate_id', $request->magistrate_id)->first();

            if ($isUnique === null) {
                $courtDetails = new CourtDetails;
                $courtDetails->county = $request->county;
                $courtDetails->court_number = $request->court_id;
                $courtDetails->magistrate_id = $request->magistrate_id;
                $courtDetails->township = $request->township;
                $courtDetails->one_defendant_up_to_2000 = $request->one_under_2000;
                $courtDetails->two_defendant_up_to_2000 = $request->two_under_2000;
                $courtDetails->one_defendant_between_2001_4000 = $request->one_btn_2000_4001;
                $courtDetails->two_defendant_between_2001_4000 = $request->two_btn_2000_4001;
                $courtDetails->one_defendant_greater_than_4000 = $request->one_over_4000;
                $courtDetails->two_defendant_greater_than_4000 = $request->two_over_4000;
                $courtDetails->one_defendant_out_of_pocket = $request->one_oop;
                $courtDetails->two_defendant_out_of_pocket = $request->two_oop;
                $courtDetails->three_defendant_up_to_2000 = $request->three_under_2000;
                $courtDetails->three_defendant_between_2001_4000 = $request->three_btn_2000_4001;
                $courtDetails->three_defendant_greater_than_4000 = $request->three_over_4000;
                $courtDetails->three_defendant_out_of_pocket = $request->three_oop;
                $courtDetails->additional_tenant = $request->additional_tenants;
                $courtDetails->digital_signature = $request->digital_signature;
                $courtDetails->online_submission = $request->online_submission;

                $courtDetails->mdj_name = $request->mdj_name;
                $courtDetails->phone_number = $request->court_number;
                $courtDetails->save();

                $geoLocation = new GeoLocation();
                $geoLocation->magistrate_id = $request->magistrate_id;
                $geoLocation->geo_locations = $request->geo_locations;
                $geoLocation->county = $request->county;
                $geoLocation->court_number = $request->court_id;
                $geoLocation->address_line_one = $request->address_line_one;
                $geoLocation->address_line_two = $request->address_line_two;
                $geoLocation->save();

                $request->session()->flash('alert-success', 'Magistrate Successfully Added!');
                $response['responseMessage'] = 'Adding Magistrate Successful!';
                $response['messageDetails'] = 'All Good';
                return $response;
            } else {
                $request->session()->flash('alert-danger', 'Magistrate Already Exists!');
                $response['responseMessage'] = 'Magistrate Already Exists!';
                $response['messageDetails'] = 'Try Again';
                return $response;
            }

        } catch ( \Exception $e ) {
            $errorDetails = 'MagistrateController - error in store() method when attempting to store magistrate';
            $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
            $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();
            \Log::error( $errorDetails . PHP_EOL . 'Error Message: ' . $e->getMessage() . PHP_EOL . 'Trace: ' . $e->getTraceAsString());
            mail( 'andrew.gaidis@gmail.com',  'Adding Magistrate Error ' . Auth::User()->id, $errorDetails );

            $returnArray['responseMessage'] = 'Bad Request';
            $returnArray['responseCode'] = 400;
            $returnArray['messageDetails'] = '' . $e->getMessage() . 'Tag could not be added to the database, please try again later';
            return response()->json($returnArray);
        }

    }

    public function getMagistrate(Request $request) {
        try {
           $geoData = GeoLocation::where('magistrate_id', $request->magistrateId)->get();
           $courtData = CourtDetails::where('magistrate_id', $request->magistrateId)->get();

            $magistrate = [
                $geoData,
                $courtData
            ];

        } catch (\Exception $e) {
            $errorDetails = 'MagistrateController - error in store() method when attempting to store magistrate';
            $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
            $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();
            \Log::error( $errorDetails . PHP_EOL . 'Error Message: ' . $e->getMessage() . PHP_EOL . 'Trace: ' . $e->getTraceAsString());
            mail( 'andrew.gaidis@gmail.com',  'Adding Magistrate Error ' . Auth::User()->id, $errorDetails );

            $returnArray['responseMessage'] = 'Bad Request';
            $returnArray['responseCode'] = 400;
            $returnArray['messageDetails'] = '' . $e->getMessage() . 'Tag could not be added to the database, please try again later';
            return response()->json($returnArray);
        }
        return $magistrate;
    }

    public function editMagistrate(Request $request) {
        Log::info('Editing a Magistrate');
        Log::info(Auth::User()->id);

        try {

            $isUnique = CourtDetails::where('id', '!=', $request->dbCourtId)->where('magistrate_id', $request->magistrateId)->first();

            if ($isUnique === null) {
                $courtDetails = CourtDetails::find($request->dbCourtId);
                $courtDetails->court_number = $request->courtId;
                $courtDetails->phone_number = $request->courtNumber;
                $courtDetails->magistrate_id = $request->magistrateId;
                $courtDetails->township = $request->township;
                $courtDetails->county = $request->county;
                $courtDetails->mdj_name = $request->mdjName;
                $courtDetails->one_defendant_up_to_2000 = $request->oneUnder2000;
                $courtDetails->one_defendant_between_2001_4000 = $request->oneBtn20004001;
                $courtDetails->one_defendant_greater_than_4000 = $request->oneOver4000;
                $courtDetails->one_defendant_out_of_pocket = $request->oneOOP;
                $courtDetails->two_defendant_up_to_2000 = $request->twoUnder2000;
                $courtDetails->two_defendant_between_2001_4000 = $request->twoBtn20004001;
                $courtDetails->two_defendant_greater_than_4000 = $request->twoOver4000;
                $courtDetails->two_defendant_out_of_pocket = $request->twoOOP;
                $courtDetails->three_defendant_up_to_2000 = $request->threeUnder2000;
                $courtDetails->three_defendant_between_2001_4000 = $request->threeBtn20004001;
                $courtDetails->three_defendant_greater_than_4000 = $request->threeOver4000;
                $courtDetails->three_defendant_out_of_pocket = $request->threeOOP;
                $courtDetails->additional_tenant = $request->additionalTenant;
                $courtDetails->digital_signature = $request->digitalSignature;
                $courtDetails->online_submission = $request->onlineSubmission;
                $courtDetails->save();


                $geoLocation = GeoLocation::find($request->dbGeoId);
                $geoLocation->magistrate_id = $request->magistrateId;
                $geoLocation->geo_locations = $request->geoLocations;
                $geoLocation->county = $request->county;
                $geoLocation->court_number = $request->courtId;
                $geoLocation->address_line_one = $request->addressOne;
                $geoLocation->address_line_two = $request->addressTwo;
                $geoLocation->save();

                $request->session()->flash('alert-success', 'Magistrate Successfully Edited!');
                $response['responseMessage'] = 'Adding Magistrate Successful!';
                $response['messageDetails'] = 'All Good';
                return $response;
            } else {
                $request->session()->flash('alert-danger', 'Magistrate Id Already Exists!');
                $response['responseMessage'] = 'Magistrate Already Exists!';
                $response['messageDetails'] = 'Try Again';
                return $response;
            }
        } catch (\Exception $e) {
            $errorDetails = 'MagistrateController - error in store() method when attempting to store magistrate';
            $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
            $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();
            \Log::error( $errorDetails . PHP_EOL . 'Error Message: ' . $e->getMessage() . PHP_EOL . 'Trace: ' . $e->getTraceAsString());
            mail( 'andrew.gaidis@gmail.com',  'Adding Magistrate Error ' . Auth::User()->id, $errorDetails );

            $returnArray['responseMessage'] = 'Bad Request';
            $returnArray['responseCode'] = 400;
            $returnArray['messageDetails'] = '' . $e->getMessage() . 'Tag could not be added to the database, please try again later';
            return response()->json($returnArray);
        }
    }

    public function delete(Request $request) {
        try {
            $dbId = CourtDetails::where('magistrate_id', $request->id)->value('id');
            CourtDetails::destroy($dbId);
            $dbId = GeoLocation::where('magistrate_id', $request->id)->value('id');
            GeoLocation::destroy($dbId);
            return $dbId;
        } catch (\Exception $e) {
            return 'failed';
        }
    }
}
