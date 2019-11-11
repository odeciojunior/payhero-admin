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
*/;

Route::group(
    [
        'middleware' => ['api'],
    ],
    function() {
        Route::post('/{version}/login', 'MobileController@login');
        Route::post('/{version}/logout', 'MobileController@logout');
    }
);

Route::group(
    [
        'middleware' => ['auth:api'],
    ],
    function() {

        Route::post('/{version}/dashboard', 'MobileController@dashboardGetData');
        Route::post('/{version}/finance', 'MobileController@financeGetData');
    }
);
