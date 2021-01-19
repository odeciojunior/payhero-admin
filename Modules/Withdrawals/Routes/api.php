<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
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

        Route::post('/withdrawals/getWithdrawalValues', 'WithdrawalsApiController@getWithdrawalValues');

        Route::get('/withdrawals/checkallowed', 'WithdrawalsApiController@checkAllowed');

        Route::get('/withdrawals/get-transactions-by-brand/{withdrawal_id}', 'WithdrawalsApiController@getTransactionsByBrand');
    }
);

