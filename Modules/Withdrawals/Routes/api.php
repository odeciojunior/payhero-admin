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
        Route::get('/old_withdrawals', 'OldWithdrawalsApiController@index')->names('api.withdrawals');
        Route::post('/old_withdrawals', 'OldWithdrawalsApiController@store')
            ->middleware('permission:finances_manage');

        Route::post('/old_withdrawals/getaccountinformation', 'OldWithdrawalsApiController@getAccountInformation');

        Route::get('/old_withdrawals/checkallowed', 'OldWithdrawalsApiController@checkAllowed');

        /**
         * News routes after Getnet
         */
        Route::get('/withdrawals', 'WithdrawalsApiController@index');
        Route::post('/withdrawals', 'WithdrawalsApiController@store')->middleware('permission:finances_manage');

        //getAccountInformation não existe no controller
        Route::post('/withdrawals/getaccountinformation', 'WithdrawalsApiController@getAccountInformation');

        Route::post('/withdrawals/getWithdrawalValues', 'WithdrawalsApiController@getWithdrawalValues')
        ->middleware('permission:finances_manage');

        Route::get('/withdrawals/checkallowed', 'WithdrawalsApiController@checkAllowed');

        Route::get('/withdrawals/get-transactions-by-brand/{withdrawal_id}', 'WithdrawalsApiController@getTransactionsByBrand');
        Route::post('/withdrawals/get-transactions/{withdrawal_id}', 'WithdrawalsApiController@getTransactions');

        
        Route::get('/withdrawals/settings', 'WithdrawalsSettingsApiController@index');
        Route::get('/withdrawals/settings/{settingsId}', 'WithdrawalsSettingsApiController@show');
        
        Route::apiResource('/withdrawals/settings', 'WithdrawalsSettingsApiController')
            ->only('store', 'update', 'destroy')
            ->names('api.withdrawals_settings')
            ->middleware('permission:finances_manage');

        Route::get('/withdrawals/settings/{companyId}/{settingsId}', 'WithdrawalsSettingsApiController@show');
    }
);

