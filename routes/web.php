<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true]);


Route::get('/password/email', 'HomeController@index')->name('home');

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');


Route::get('/dashboard', 'DashboardController@index')->middleware('auth');
Route::post('/dashboard/download', 'DashboardController@downloadPDF')->middleware('auth');
Route::post('/dashboard/statusChange', 'DashboardController@statusChange')->middleware('auth');
Route::post('/dashboard/delete', 'EvictionController@delete')->middleware('auth');
Route::get('/dashboard/getCourtDate', 'DashboardController@getCourtDate')->middleware('auth');
Route::post('/dashboard/storeCourtDate', 'DashboardController@storeCourtDate')->middleware('auth');

/* Eviction Creator */
Route::get('/new-ltc', 'EvictionController@index')->middleware('auth');
Route::post('/new-ltc/pdf-data', 'EvictionController@formulatePDF')->middleware('auth');

/* Order of Possession Creator */
Route::get('/new-oop', 'OrderOfPossessionController@index')->middleware('auth');
Route::post('/new-oop/pdf-data', 'OrderOfPossessionController@formulatePDF')->middleware('auth');

/* Civil Complaint Creator */
Route::get('/new-civil-complaint', 'CivilComplaintController@index')->middleware('auth');
Route::post('/new-civil-complaint/pdf-data', 'CivilComplaintController@formulatePDF')->middleware('auth');

/* Informational Pages */
Route::get('/information', 'InfoController@index');
Route::get('/FAQ', 'FAQController@index');
Route::get('/where-does-this-work', 'WhereDoesThisWorkController@index');
Route::post('/where-does-this-work', 'WhereDoesThisWorkController@store');
Route::get('/about-us', 'AboutUsController@index');

/* Magistrate Creator */
Route::get('/magistrateCreator', 'MagistrateController@index')->middleware('auth');
Route::get('/magistrateCreator/getMagistrate', 'MagistrateController@getMagistrate')->middleware('auth');
Route::post('/magistrateCreator/editMagistrate', 'MagistrateController@editMagistrate')->middleware('auth');
Route::post('/magistrateCreator', 'MagistrateController@store')->middleware('auth');
Route::post('/magistrateCreator/delete', 'MagistrateController@delete')->middleware('auth');

/* User Management */
Route::get('/userManagement', 'UserManagementController@index')->middleware('auth');
Route::post('/userManagement/deleteUser', 'UserManagementController@deleteUser')->middleware('auth');
Route::post('/userManagement/changeRole', 'UserManagementController@changeUserRole')->middleware('auth');
Route::post('/userManagement/changeCourt', 'UserManagementController@changeCourt')->middleware('auth');



Route::get('command/migrate', function () {
    $exitCode = \Artisan::call('migrate');
    dd("Done");
});