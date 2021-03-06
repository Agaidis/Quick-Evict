<?php

namespace App\Http\Controllers;

use App\CivilUnique;
use App\CourtDetails;
use App\ErrorLog;
use App\GeoLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


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

            $courtDetails = DB::table('court_details')
                ->leftJoin('civil_unique', 'court_details.id', '=', 'civil_unique.court_details_id')
                ->orderBy('court_details.county')
                ->get();

            return view('magistrateCreator', compact('courtDetails'));
        }
    }

    public function store(Request $request) {
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
                $courtDetails->oop_additional_tenant_fee = $request->oop_additional_tenant_fee;
                $courtDetails->civil_mail_additional_tenant_fee = $request->civil_mail_additional_tenant_fee;
                $courtDetails->civil_constable_additional_tenant_fee = $request->civil_constable_additional_tenant_fee;
                $courtDetails->digital_signature = $request->is_digital_signature_allowed === 'on' ? 1 : 0;
                $courtDetails->is_distance_fee = $request->is_ltc_driving_fee_allowed === 'on' ? 1 : 0;
                $courtDetails->oop_distance_fee = $request->is_oop_driving_fee_allowed === 'on' ? 1 : 0 ;
                $courtDetails->civil_distance_fee = $request->is_civil_driving_fee_allowed === 'on' ? 1 : 0;
                $courtDetails->online_submission = $request->online_submission;

                $courtDetails->mdj_name = $request->mdj_name;
                $courtDetails->phone_number = $request->court_number;
                $courtDetails->save();

                $civilUnique = new CivilUnique();
                $civilUnique->court_details_id = $courtDetails->id;
                $civilUnique->under_500_1_def_mail = $request->one_under_500_mailed;
                $civilUnique->btn_500_2000_1_def_mail = $request->one_btn_500_2000_mailed;
                $civilUnique->btn_2000_4000_1_def_mail = $request->one_btn_2000_4000_mailed;
                $civilUnique->btn_4000_12000_1_def_mail = $request->one_btn_4000_12000_mailed;
                $civilUnique->under_500_2_def_mail = $request->two_under_500_mailed;
                $civilUnique->btn_500_2000_2_def_mail = $request->two_btn_500_2000_mailed;
                $civilUnique->btn_2000_4000_2_def_mail = $request->two_btn_2000_4000_mailed;
                $civilUnique->btn_4000_12000_2_def_mail = $request->two_btn_4000_12000_mailed;
                $civilUnique->under_500_1_def_constable = $request->one_under_500_constable;
                $civilUnique->btn_500_2000_1_def_constable = $request->one_btn_500_2000_constable;
                $civilUnique->btn_2000_4000_1_def_constable = $request->one_btn_2000_4000_constable;
                $civilUnique->btn_4000_12000_1_def_constable = $request->one_btn_4000_12000_constable;
                $civilUnique->under_500_2_def_constable = $request->two_under_500_constable;
                $civilUnique->btn_500_2000_2_def_constable = $request->two_btn_500_2000_constable;
                $civilUnique->btn_2000_4000_2_def_constable = $request->two_btn_2000_4000_constable;
                $civilUnique->btn_4000_12000_2_def_constable = $request->two_btn_4000_12000_constable;
                $civilUnique->save();

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
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();

            $errorDetails = 'MagistrateController - error in store() method when attempting to store magistrate';
            $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
            $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();
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
            $courtData = CourtDetails::where('magistrate_id', $request->magistrateId)->first();
            $civilData = CivilUnique::where('court_details_id', $courtData->id)->first();

            if ($civilData === null || $civilData === '') {
                $civilData = 'empty';
            }

            $magistrate = [
                $geoData,
                $courtData,
                $civilData
            ];

        } catch (\Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            $errorDetails = 'MagistrateController - error in store() method when attempting to store magistrate';
            $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
            $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();

            mail( 'andrew.gaidis@gmail.com',  'Adding Magistrate Error ' . Auth::User()->id, $errorDetails );

            $returnArray['responseMessage'] = 'Bad Request';
            $returnArray['responseCode'] = 400;
            $returnArray['messageDetails'] = '' . $e->getMessage() . 'Tag could not be added to the database, please try again later';
            return response()->json($returnArray);
        }
        return $magistrate;
    }

    public function editMagistrate(Request $request) {

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
                $courtDetails->oop_additional_tenant_fee = $request->oopAdditionalTenant;
                $courtDetails->civil_mail_additional_tenant_fee = $request->civilMailedAdditionalTenant;
                $courtDetails->civil_constable_additional_tenant_fee = $request->civilConstableAdditionalTenant;
                $courtDetails->digital_signature = $request->digitalSignature  === 'true' ? 1 : 0;
                $courtDetails->is_distance_fee = $request->drivingFee  === 'true' ? 1 : 0;
                $courtDetails->oop_distance_fee = $request->oopDrivingFee  === 'true' ? 1 : 0;
                $courtDetails->civil_distance_fee = $request->civilDrivingFee  === 'true' ? 1 : 0;
                $courtDetails->online_submission = $request->onlineSubmission;
                $courtDetails->save();

                if ($request->dbCivilId != '') {
                    $civilUnique = CivilUnique::find($request->dbCivilId);
                    $civilUnique->court_details_id = $request->dbCourtId;
                    $civilUnique->under_500_1_def_mail = $request->oneUnder500Mailed;
                    $civilUnique->btn_500_2000_1_def_mail = $request->oneBtn500And2000;
                    $civilUnique->btn_2000_4000_1_def_mail = $request->oneBtn2000And4000Mailed;
                    $civilUnique->btn_4000_12000_1_def_mail = $request->oneBtn4000And12000Mailed;
                    $civilUnique->under_500_2_def_mail = $request->twoUnder500Mailed;
                    $civilUnique->btn_500_2000_2_def_mail = $request->twoBtn500And2000Mailed;
                    $civilUnique->btn_2000_4000_2_def_mail = $request->twoBtn2000And4000Mailed;
                    $civilUnique->btn_4000_12000_2_def_mail = $request->twoBtn4000And12000Mailed;
                    $civilUnique->under_500_1_def_constable = $request->oneUnder500Constable;
                    $civilUnique->btn_500_2000_1_def_constable = $request->oneBtn500And2000Constable;
                    $civilUnique->btn_2000_4000_1_def_constable = $request->oneBtn2000And4000Constable;
                    $civilUnique->btn_4000_12000_1_def_constable = $request->oneBtn4000And12000Constable;
                    $civilUnique->under_500_2_def_constable = $request->twoUnder500Constable;
                    $civilUnique->btn_500_2000_2_def_constable = $request->twoBtn500And2000Constable;
                    $civilUnique->btn_2000_4000_2_def_constable = $request->twoBtn2000And4000Constable;
                    $civilUnique->btn_4000_12000_2_def_constable = $request->twoBtn4000And12000Constable;
                    $civilUnique->save();
                } else {
                    $civilUnique = new CivilUnique();
                    $civilUnique->court_details_id = $request->dbCourtId;
                    $civilUnique->under_500_1_def_mail = $request->oneUnder500Mailed;
                    $civilUnique->btn_500_2000_1_def_mail = $request->oneBtn500And2000;
                    $civilUnique->btn_2000_4000_1_def_mail = $request->oneBtn2000And4000Mailed;
                    $civilUnique->btn_4000_12000_1_def_mail = $request->oneBtn4000And12000Mailed;
                    $civilUnique->under_500_2_def_mail = $request->twoUnder500Mailed;
                    $civilUnique->btn_500_2000_2_def_mail = $request->twoBtn500And2000Mailed;
                    $civilUnique->btn_2000_4000_2_def_mail = $request->twoBtn2000And4000Mailed;
                    $civilUnique->btn_4000_12000_2_def_mail = $request->twoBtn4000And12000Mailed;
                    $civilUnique->under_500_1_def_constable = $request->oneUnder500Constable;
                    $civilUnique->btn_500_2000_1_def_constable = $request->oneBtn500And2000Constable;
                    $civilUnique->btn_2000_4000_1_def_constable = $request->oneBtn2000And4000Constable;
                    $civilUnique->btn_4000_12000_1_def_constable = $request->oneBtn4000And12000Constable;
                    $civilUnique->under_500_2_def_constable = $request->twoUnder500Constable;
                    $civilUnique->btn_500_2000_2_def_constable = $request->twoBtn500And2000Constable;
                    $civilUnique->btn_2000_4000_2_def_constable = $request->twoBtn2000And4000Constable;
                    $civilUnique->btn_4000_12000_2_def_constable = $request->twoBtn4000And12000Constable;
                    $civilUnique->save();
                }


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
        } catch (Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }

    public function delete(Request $request) {
        try {
            $courtDetailsId = CourtDetails::where('magistrate_id', $request->id)->value('id');
            $civilUniqueId = CivilUnique::where('court_details_id', $courtDetailsId)->value('id');
            $dbId = GeoLocation::where('magistrate_id', $request->id)->value('id');

            CivilUnique::destroy($civilUniqueId);
            GeoLocation::destroy($dbId);
            CourtDetails::destroy($courtDetailsId);

            return $dbId;
        } catch (\Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'failed';
        }
    }
}