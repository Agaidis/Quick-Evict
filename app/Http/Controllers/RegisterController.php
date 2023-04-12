<?php

namespace App\Http\Controllers;

use App\ErrorLog;


class DashboardController extends Controller
{
    public function index()
    {

        try {
            $companies = 'haha';


            return view('register', compact('companies'));
        } catch (\Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
            $errorMsg->save();
        }

    }
}