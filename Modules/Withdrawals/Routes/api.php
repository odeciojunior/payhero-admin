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

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin', 'setUserAsLogged'],
    ],
    function () {
        /**
         * Old routes before getnet
         */
        Route::apiResource('/old_withdrawals', 'OldWithdrawalsApiController')
            ->only('index', 'store')
            ->names('api.withdrawals');

        Route::post('/old_withdrawals/getaccountinformation', 'OldWithdrawalsApiController@getAccountInformation');

        Route::get('/old_withdrawals/checkallowed', 'OldWithdrawalsApiController@checkAllowed');

        /**
         * News routes after Getnet
         */
        Route::apiResource('/withdrawals', 'WithdrawalsApiController')
            ->only('index', 'store')
            ->names('api.withdrawals');

        Route::post('/withdrawals/getaccountinformation', 'WithdrawalsApiController@getAccountInformation');

        Route::get('/withdrawals/checkallowed', 'WithdrawalsApiController@checkAllowed');
    }
);

