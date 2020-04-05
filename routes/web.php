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

Route::get('/dashboard/edit-file', 'DashboardController@getFileData')->middleware('auth');;
Route::post('/dashboard/edit-oop', 'DashboardController@editOOP')->middleware('auth');;
Route::post('/dashboard/edit-civil', 'DashboardController@editCivil')->middleware('auth');

/* New File */
Route::get('/new-file', 'NewFileController@index')->middleware('auth');
Route::post('/new-file', 'NewFileController@proceedToFileTypeWithSelectedCounty')->middleware('auth');
Route::get('/new-file/get-court-fee', 'NewFileController@getFilingFee')->middleware('auth');


/* Eviction Creator */
Route::post('/new-ltc/pdf-data', 'EvictionController@formulatePDF')->middleware('auth');
Route::post('/new-ltc/sample-pdf', 'EvictionController@showSamplePDF')->middleware('auth');

/* Order of Possession Creator */
Route::post('/new-oop/pdf-data', 'OrderOfPossessionController@formulatePDF')->middleware('auth');
Route::post('/new-oop/sample-pdf', 'OrderOfPossessionController@showSamplePDF')->middleware('auth');

/* Civil Complaint Creator */
Route::post('/new-civil-complaint/pdf-data', 'CivilComplaintController@formulatePDF')->middleware('auth');
Route::post('/new-civil-complaint/sample-pdf', 'CivilComplaintController@showSamplePDF')->middleware('auth');

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

Route::post('/get-signature-type', 'EvictionController@getDigitalSignature')->middleware('auth');

Route::get('/store', 'FileStorageController@testFile')->middleware('auth');
Route::post('/get-filings', 'FileStorageController@getFilings')->middleware('auth');
Route::post('/download-filings', 'FileStorageController@downloadFilings')->middleware('auth');
Route::post('/file-upload', 'FileStorageController@storeFile')->middleware('auth');

/* General Admin */
Route::get('/generalAdmin', 'GeneralAdminController@index')->middleware('auth');

Route::post('/generalAdmin', 'GeneralAdminController@updateDrivingFee')->middleware('auth');

Route::get('/getFileFee', 'GetFileFeeController@view');

Route::post('get-file-fee', 'GetFileFeeController@index')->middleware('auth');

Route::post('get-file-fee/fee', 'GetFileFeeController@getFilingFee')->middleware('auth');

/* Fee Duplicator */
Route::get('/feeDuplicator', 'FeeDuplicatorController@index')->middleware('auth');
Route::get('/feeDuplicator/getMagistrates', 'FeeDuplicatorController@getSelectedCourtMagistrates')->middleware('auth');
Route::post('/feeDuplicator', 'FeeDuplicatorController@duplicateFees')->middleware('auth');


Route::get('command/migrate', function () {
    $exitCode = \Artisan::call('migrate');
    dd("Done");
});