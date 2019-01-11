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
//        if (Auth::guest()) {
//            return view('/login');
//        } else {
        
        return view('whereDoesThisWork');
//        }
    }

    public function store(Request $request) {
        try {
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
            $headers .= "From: EvictionTechSupport@EvictionTech.com\r\n";

            $emailTo = 'andrew.gaidis@gmail.com, chad@slatehousegroup.com';
            $message = '<html><body><span>Email: ' . $request->email . '</span><br>' . '<span>Area of Interest: ' . $request->area_of_interest . '</span></body></html>';

            mail($emailTo, 'EvictionTech Email Submission', $message, $headers);

            $request->session()->flash('alert-success', 'Thank you for your Interest!');

            return view('whereDoesThisWork');

        } catch( \Exception $e ) {
            $errorDetails = 'WhereDoesThisWorkController - error in store() method when attempting to send Email';
            $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
            $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();
            \Log::error($errorDetails . PHP_EOL . 'Error Message: ' . $e->getMessage() . PHP_EOL . 'Trace: ' . $e->getTraceAsString());
            mail('andrew.gaidis@gmail.com', 'Emailing Details Error', $errorDetails);
            $request->session()->flash('alert-danger', 'Something Went Wrong Sending Email.');
            return view('whereDoesThisWork');
        }
    }
}
