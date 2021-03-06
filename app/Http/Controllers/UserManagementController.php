<?php
/**
 * Created by PhpStorm.
 * User: andrewgaidis
 * Date: 1/17/19
 * Time: 10:50 AM
 */
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use App\CourtDetails;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{

    /**
     * Create a new controller instance.
     *
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        if (Auth::guest()) {
            return view('/login');
        } else {
            $users = User::all();
            $courtNumbers = DB::table('court_details')->select('court_number')->orderBy('court_number', 'ASC')->distinct()->get();

            return view('userManagement', compact(
                'users',
                'courtNumbers'));
        }
    }

    /**
     * @param $request
     *
     * @return string
     *
     */
    public function changeUserRole(Request $request)
    {
        $returnArray = array();

        try {
            $user = User::find($request->id);
            $user->role = $request->role;
            $user->save();


            $returnArray['id'] = $request->id;
            $returnArray['role'] = $request->role;

            return $returnArray;
        } catch (\Exception $e) {
            mail('andrew.gaidis@gmail.com', 'Admin Issue', 'There was an issue with changing a users role');
            return 'failed';
        }
    }

    /**
     * @param $request
     *
     * @return string
     *
     */
    public function changeCourt(Request $request)
    {
        try {
            $user = User::find($request->id);
            $user->court_id = $request->court;
            $user->save();

            return 'success';
        } catch (\Exception $e) {
            mail('andrew.gaidis@gmail.com', 'Admin Issue', 'There was an issue with changing a users court');
            return 'failed';
        }
    }

    /**
     * @param $request
     *
     * @return string
     *
     */
    public function changeCourtId(Request $request)
    {
        try {
            $user = User::find($request->id);
            $user->court_id = $request->court_id;
            $user->save();

            return 'success';
        } catch (\Exception $e) {
            mail('andrew.gaidis@gmail.com', 'Admin Issue', 'There was an issue with changing a users court_id');
            return 'failed';
        }
    }

    /**
     * @param $request
     *
     * @return string
     *
     */
    public function deleteUser(Request $request)
    {
        try {
            User::destroy($request->id);

            $request->session()->flash('alert-success', 'User has been Successfully Deleted!');

            return 'success';
        } catch (\Exception $e) {
            mail('andrew.gaidis@gmail.com', 'Admin Issue', 'There was an issue with deleting a user');
            return 'failed';
        }
    }
}