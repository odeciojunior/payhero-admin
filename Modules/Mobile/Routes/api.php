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
        Route::post('/{version}/finance/withdraw', 'MobileController@financeWithdraw');
        Route::post('/{version}/finance/account', 'MobileController@financeAccountInformation');
        Route::post('/{version}/profile', 'MobileController@profileGetData');
        Route::post('/{version}/sales', 'MobileController@salesByFilter');
        Route::post('/{version}/sales/details', 'MobileController@getSaleDetails');
        Route::post('/{version}/projects', 'MobileController@getUserProjects');
    }
);
