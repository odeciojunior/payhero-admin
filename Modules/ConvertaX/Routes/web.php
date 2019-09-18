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

//Route::prefix('convertax')->group(function() {
//    Route::get('/', 'ConvertaXController@index');
//});
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::Resource('apps/convertax', 'ConvertaXController')
         ->only('index', 'create', 'store', 'edit', 'update', 'show', 'destroy');
    Route::get('/getconvertaxintegrations', 'ConvertaXController@getIntegrations');
});
