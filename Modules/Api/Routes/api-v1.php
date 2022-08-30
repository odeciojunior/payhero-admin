<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([ "middleware" => "authApiV1" ], function () {
    Route::post('subsellers', 'V1\SubsellersApiController@createSubseller');
    Route::get('sales','V1\SalesApiController@index');
});
