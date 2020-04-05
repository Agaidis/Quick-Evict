<?php

namespace App\Http\Controllers;

use App\CivilUnique;
use App\CourtDetails;
use App\ErrorLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            $magistrates = CourtDetails::where('court_number', $request->courtNumber)->orderBy('magistrate_id')->get();

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
            Log::info($request->magistrates);
            $courtFees = CourtDetails::where('magistrate_id', $request->duplicated_magistrate)->first();
            $civilFees = CivilUnique::where('court_details_id', $courtFees->id)->first();

            foreach ($request->magistrates as $magistrate) {

                $magistrateId = CourtDetails::where('magistrate_id', $magistrate)->value('id');
                $updateCourtDetails = CourtDetails::where('magistrate_id', $magistrate)->update(
                    [
                        'one_defendant_up_to_2000' => $courtFees->one_defendant_up_to_2000,
                        'two_defendant_up_to_2000' => $courtFees->two_defendant_up_to_2000,
                        'one_defendant_between_2001_4000' => $courtFees->one_defendant_between_2001_4000,
                        'two_defendant_between_2001_4000' => $courtFees->two_defendant_between_2001_4000,
                        'one_defendant_greater_than_4000' => $courtFees->one_defendant_greater_than_4000,
                        'two_defendant_greater_than_4000' => $courtFees->two_defendant_greater_than_4000,
                        'one_defendant_out_of_pocket' => $courtFees->one_defendant_out_of_pocket,
                        'two_defendant_out_of_pocket' => $courtFees->two_defendant_out_of_pocket,
                        'three_defendant_up_to_2000' => $courtFees->three_defendant_up_to_2000,
                        'three_defendant_between_2001_4000' => $courtFees->three_defendant_between_2001_4000,
                        'three_defendant_greater_than_4000' => $courtFees->three_defendant_greater_than_4000,
                        'three_defendant_out_of_pocket' => $courtFees->three_defendant_out_of_pocket,
                        'additional_tenant' => $courtFees->additional_tenant,
                        'oop_additional_tenant_fee' => $courtFees->oop_additional_tenant_fee,
                        'civil_mail_additional_tenant_fee' => $courtFees->civil_mail_additional_tenant_fee,
                        'civil_constable_additional_tenant_fee' => $courtFees->civil_constable_additional_tenant_fee
                    ]
                );

                $doesCivilDetailsExist = CivilUnique::where('court_details_id', $magistrateId)->get();

                if ($doesCivilDetailsExist->isEmpty()) {
                    $civilUnique = new CivilUnique();
                    $civilUnique->court_details_id = $magistrateId;
                    $civilUnique->under_500_1_def_mail = $civilFees->under_500_1_def_mail;
                    $civilUnique->btn_500_2000_1_def_mail = $civilFees->btn_500_2000_1_def_mail;
                    $civilUnique->btn_2000_4000_1_def_mail = $civilFees->btn_2000_4000_1_def_mail;
                    $civilUnique->btn_4000_12000_1_def_mail = $civilFees->btn_4000_12000_1_def_mail;
                    $civilUnique->under_500_2_def_mail = $civilFees->under_500_2_def_mail;
                    $civilUnique->btn_500_2000_2_def_mail = $civilFees->btn_500_2000_2_def_mail;
                    $civilUnique->btn_2000_4000_2_def_mail = $civilFees->btn_2000_4000_2_def_mail;
                    $civilUnique->btn_4000_12000_2_def_mail = $civilFees->btn_4000_12000_2_def_mail;
                    $civilUnique->under_500_1_def_constable = $civilFees->under_500_1_def_constable;
                    $civilUnique->btn_500_2000_1_def_constable = $civilFees->btn_500_2000_1_def_constable;
                    $civilUnique->btn_2000_4000_1_def_constable = $civilFees->btn_2000_4000_1_def_constable;
                    $civilUnique->btn_4000_12000_1_def_constable = $civilFees->btn_4000_12000_1_def_constable;
                    $civilUnique->under_500_2_def_constable = $civilFees->under_500_2_def_constable;
                    $civilUnique->btn_500_2000_2_def_constable = $civilFees->btn_500_2000_2_def_constable;
                    $civilUnique->btn_2000_4000_2_def_constable = $civilFees->btn_2000_4000_2_def_constable;
                    $civilUnique->btn_4000_12000_2_def_constable = $civilFees->btn_4000_12000_2_def_constable;
                    $civilUnique->save();
                } else {
                    CivilUnique::where('court_details_id', $updateCourtDetails->id)->update(
                        [
                            'under_500_1_def_mail' => $civilFees->under_500_1_def_mail,
                            'btn_500_2000_1_def_mail' => $civilFees->btn_500_2000_1_def_mail,
                            'btn_2000_4000_1_def_mail' => $civilFees->btn_2000_4000_1_def_mail,
                            'btn_4000_12000_1_def_mail' => $civilFees->btn_4000_12000_1_def_mail,
                            'under_500_2_def_mail' => $civilFees->under_500_2_def_mail,
                            'btn_500_2000_2_def_mail' => $civilFees->btn_500_2000_2_def_mail,
                            'btn_2000_4000_2_def_mail' => $civilFees->btn_2000_4000_2_def_mail,
                            'btn_4000_12000_2_def_mail' => $civilFees->btn_4000_12000_2_def_mail,
                            'under_500_1_def_constable' => $civilFees->under_500_1_def_constable,
                            'btn_500_2000_1_def_constable' => $civilFees->btn_500_2000_1_def_constable,
                            'btn_2000_4000_1_def_constable' => $civilFees->btn_2000_4000_1_def_constable,
                            'btn_4000_12000_1_def_constable' => $civilFees->btn_4000_12000_1_def_constable,
                            'under_500_2_def_constable' => $civilFees->under_500_2_def_constable,
                            'btn_500_2000_2_def_constable' => $civilFees->btn_500_2000_2_def_constable,
                            'btn_2000_4000_2_def_constable' => $civilFees->btn_2000_4000_2_def_constable,
                            'btn_4000_12000_2_def_constable' => $civilFees->btn_4000_12000_2_def_constable
                        ]
                    );
                }
            }

            $request->session()->flash('alert-success', $request->court_number . ' Fees Have been Duplicated!');
            return back();

        } catch (Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();

            $request->session()->flash('alert-danger', 'Server Error while Duplicating');
            return back();
        }
    }
}
