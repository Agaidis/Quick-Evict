<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhereDoesThisWorkController extends Controller
{
        /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::guest()) {
            return view('/login');
        } else {
        
        return view('whereDoesThisWork');
        }
    }
}
