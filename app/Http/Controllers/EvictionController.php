<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use GMaps;
use Barryvdh\DomPDF\Facade as PDF;
use Dompdf\Dompdf;



class EvictionController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            return view('eviction', compact('map'));

    }

    public function formulatePDF() {

        try {
            $additionalRent = $_POST['addit_rent'];
            $attorneyFees = $_POST['attorney_fees'];
            $damageAmt = $_POST['damage_amt'];
            $filing_date = $_POST['filing_date'];
            $landlord = $_POST['landlord'];
            $leaseStatus = $_POST['lease_status'];
            $leaseType = $_POST['lease_type'];
            $ownerName = $_POST['owner_name'];
            $ownerPhone = $_POST['owner_phone'];
            $quitNotice = $_POST['quit_notice'];
            $rentedBy = $_POST['rented_by'];
            $termLease = $_POST['term_lease'];
            $unjustDamages = $_POST['unjust_damages'];
            $unjustDamages = str_replace('$', '', $unjustDamages);
            $defendantState = $_POST['state'];
            $defendantZipcode = $_POST['zipcode'];
            $defendantCounty = $_POST['county'];
            $defendanthouseNum = $_POST['houseNum'];
            $defendantStreetName= $_POST['streetName'];
            $defendantTown = $_POST['town'];

            $dompdf = new Dompdf();
            $dompdf->loadHtml('');

            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->stream();

            //return view('eviction', compact('map'));

        } catch ( \Exception $e) {
            return back();
            mail('andrew.gaidis@gmail.com', 'formulatePDF Error', $e->getMessage());
        }
        return back();
    }

//    public function addFile(Request $request) {
//        try {
//            mail('andrew.gaidis@gmail.com', 'formulatePDF Success', $request);
//            $request->pdf->storeAs('pdf', $request->pdf->getClientOriginalName());
//            mail('andrew.gaidis@gmail.com', 'formulatePDF Success', $_POST['pdf']);
//            return $_POST;
//        } catch ( \Exception $e) {
//            mail('andrew.gaidis@gmail.com', 'adding File Error', $e->getMessage());
//        }
//    }
}