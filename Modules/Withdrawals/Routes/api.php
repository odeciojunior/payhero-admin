<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin", "demo_account"],
    ],
    function () {

        Route::get('/withdrawals', 'WithdrawalsApiController@index');
        Route::post('/withdrawals', 'WithdrawalsApiController@store')->middleware('permission:finances_manage');

        Route::post('/withdrawals/getaccountinformation', 'WithdrawalsApiController@getAccountInformation');

        Route::post('/withdrawals/getWithdrawalValues', 'WithdrawalsApiController@getWithdrawalValues')
                ->middleware('permission:finances_manage');

        Route::get('/withdrawals/checkallowed', 'WithdrawalsApiController@checkAllowed');

        Route::get('/withdrawals/get-transactions-by-brand/{withdrawal_id}', 'WithdrawalsApiController@getTransactionsByBrand');
        Route::post('/withdrawals/get-transactions/{withdrawal_id}', 'WithdrawalsApiController@getTransactions');

        Route::get('/withdrawals/settings', 'WithdrawalsSettingsApiController@index');
        Route::get('/withdrawals/settings/{companyId}', 'WithdrawalsSettingsApiController@show');

        Route::apiResource('/withdrawals/settings', 'WithdrawalsSettingsApiController')
                ->only('store', 'update', 'destroy')
                ->names('api.withdrawals_settings_api')
                ->middleware('permission:finances_manage');

        Route::get('/withdrawals/settings/{companyId}/{settingsId}', 'WithdrawalsSettingsApiController@show');

        Route::get('/withdrawals/get-resume/', 'WithdrawalsApiController@getResume');
    }
);
