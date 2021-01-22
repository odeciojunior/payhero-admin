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
        Route::post('/withdrawals/get-transactions/{withdrawal_id}', 'WithdrawalsApiController@getTransactions');

        Route::apiResource('/withdrawals/settings', 'WithdrawalsSettingsApiController')
            ->only('index', 'show', 'store', 'update', 'destroy')
            ->names('api.withdrawals_settings')
            ->middleware('role:account_owner|admin');

        Route::get('/withdrawals/settings/{companyId}/{settingsId}', 'WithdrawalsSettingsApiController@show');
    }
);

