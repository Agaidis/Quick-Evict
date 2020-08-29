<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\CourtDetails;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $counties = CourtDetails::distinct()->orderBy('county')->get(['county']);

        return view('home', compact('counties'));
    }
}

