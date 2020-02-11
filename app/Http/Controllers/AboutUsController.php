<?php

namespace App\Http\Controllers;

use App\ErrorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class AboutUsController extends Controller
{
            /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return view('aboutUs');
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }

    }
}
