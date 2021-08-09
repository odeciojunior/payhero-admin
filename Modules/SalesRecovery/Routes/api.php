<?php

use Illuminate\Support\Facades\Route;
// role:account_owner|admin|attendance
Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin', 'permission:sales_recovery'],
    ],
    function() {
        Route::apiResource('recovery', 'SalesRecoveryApiController')->only('index')
             ->names('api.recovery');

        Route::get('recovery/getrecoverydata', 'SalesRecoveryApiController@getRecoveryData');
        Route::get('checkout/getrecoverydata', 'SalesRecoveryApiController@getRecoveryData');
        Route::get('recovery/getrefusedcart', 'SalesRecoveryApiController@getCartRefused');
        Route::get('recovery/getboleto', 'SalesRecoveryApiController@getBoletoOverdue');
        Route::get('recovery/get-pix', 'SalesRecoveryApiController@getPixOverdue');

        Route::post('recovery/details', 'SalesRecoveryApiController@getDetails');
        Route::post('recovery/regenerateboleto', 'SalesRecoveryApiController@regenerateBoleto');
        Route::post('recovery/export', 'SalesRecoveryApiController@export');
    }
);
