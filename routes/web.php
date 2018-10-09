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




Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/online-eviction', 'EvictionController@index');

Route::get('/eviction-info', 'EvictionInfoController@index');

Route::get('/FAQ', 'FAQController@index');

Route::get('/where-does-this-work', 'WhereDoesThisWorkController@index');

Route::get('/about-us', 'AboutUsController@index');

Route::get('/magistrateCreator', 'MagistrateController@index');

Route::post('/magistrateCreator', 'MagistrateController@store');

Route::post('/magistrateCreator/delete', 'MagistrateController@delete');

Route::post('/online-eviction/pdf-data', 'EvictionController@formulatePDF');

Route::post('/online-eviction/add-file', 'EvictionController@addFile');

Route::get('command/migrate', function () {
    $exitCode = \Artisan::call('migrate');
    dd("Done");
});