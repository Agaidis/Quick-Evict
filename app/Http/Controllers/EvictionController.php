<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GMaps;

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
            mail('andrew.gaidis@gmail.com', 'formulatePDF Success', $_POST);
        } catch ( \Exception $e) {
            mail('andrew.gaidis@gmail.com', 'formulatePDF Error', $e);
        }
    }
}