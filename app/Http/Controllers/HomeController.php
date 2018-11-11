<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Evictions;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    //    $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $evictions = Evictions::all();
            return view('home', compact('evictions'));
        } catch ( \Exception $e ) {
            $errorDetails = 'HomeController - error in store() method when attempting to store magistrate';
            $errorDetails .= PHP_EOL . 'File: ' . $e->getFile();
            $errorDetails .= PHP_EOL . 'Line #' . $e->getLine();
            \Log::error( $errorDetails . PHP_EOL . 'Error Message: ' . $e->getMessage() . PHP_EOL . 'Trace: ' . $e->getTraceAsString());
            mail( 'andrew.gaidis@gmail.com',  'Showing Home Page', $errorDetails );
        }

    }
}
