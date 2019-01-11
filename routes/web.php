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

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/dashboard', 'DashboardController@index');
Route::post('/dashboard/download', 'DashboardController@downloadPDF');
Route::post('/dashboard/statusChange', 'DashboardController@statusChange');
Route::post('/dashboard/delete', 'EvictionController@delete');

Route::get('/dashboard/getCourtDate', 'DashboardController@getCourtDate');
Route::post('/dashboard/storeCourtDate', 'DashboardController@storeCourtDate');

Route::get('/online-eviction', 'EvictionController@index');
Route::post('/online-eviction/saveSignature', 'EvictionController@saveSignature');

Route::get('/eviction-info', 'EvictionInfoController@index');

Route::get('/FAQ', 'FAQController@index');

Route::get('/where-does-this-work', 'WhereDoesThisWorkController@index');
Route::post('/where-does-this-work', 'WhereDoesThisWorkController@store');

Route::get('/about-us', 'AboutUsController@index');

Route::get('/magistrateCreator', 'MagistrateController@index');

Route::get('/magistrateCreator/getMagistrate', 'MagistrateController@getMagistrate');

Route::post('/magistrateCreator/editMagistrate', 'MagistrateController@editMagistrate');

Route::post('/magistrateCreator', 'MagistrateController@store');

Route::post('/magistrateCreator/delete', 'MagistrateController@delete');

Route::post('/online-eviction/pdf-data', 'EvictionController@formulatePDF');

Route::post('/online-eviction/add-file', 'EvictionController@addFile');

Route::get('command/migrate', function () {
    $exitCode = \Artisan::call('migrate');
    dd("Done");
});