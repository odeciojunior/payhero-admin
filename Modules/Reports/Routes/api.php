<?php

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

Route::group(
    [
        'middleware' => ['web', 'auth'],
    ],
    function() {
        Route::apiResource('reports', 'ReportsApiController')->only('index');

        Route::get('reports/getsalesbyorigin', 'ReportsApiController@getSalesByOrigin');
    }
);
