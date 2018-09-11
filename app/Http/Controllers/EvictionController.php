<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GMaps;
use mikehaertl\pdftk\Pdf;
use Illuminate\Support\Facades\Storage;


class EvictionController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        if (Auth::guest()) {
//            return view('/login');
//        } else {

            return view('eviction', compact('map'));
     //   }
    }
    
    //When they click next step.. call this function to take their address and turn it into the maps view
    public function getMapLocation() {
    
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
            $propertyAddressLine1 = $_POST['property_address_1'];
            $propertyAddressLine2 = $_POST['property_address_2'];
            $quitNotice = $_POST['quit_notice'];
            $rentedBy = $_POST['rented_by'];
            $termLease = $_POST['term_lease'];
            $unjustDamages = $_POST['unjust_damages'];

            $contents = asset('storage/Landlordand Tenant Complaint.pdf');
            return $contents;

            $contents = Storage::get('Landlordand Tenant Complaint.pdf');
            return $contents;

            mail('andrew.gaidis@gmail.com', 'formulatePDF Success', $contents);
            mail('andrew.gaidis@gmail.com', 'formulatePDF Success', 'Success!');
            return $_POST;
        } catch ( \Exception $e) {
            mail('andrew.gaidis@gmail.com', 'formulatePDF Error', $e->getMessage());
        }
    }

    public function addFile(Request $request) {
        try {
            mail('andrew.gaidis@gmail.com', 'formulatePDF Success', $request);
            $request->pdf->storeAs('pdf', $request->pdf->getClientOriginalName());
            mail('andrew.gaidis@gmail.com', 'formulatePDF Success', $_POST['pdf']);
            return $_POST;
        } catch ( \Exception $e) {
            mail('andrew.gaidis@gmail.com', 'adding File Error', $e->getMessage());
        }
    }
}