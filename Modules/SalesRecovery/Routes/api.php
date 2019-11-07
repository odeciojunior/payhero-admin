<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'role:account_owner|admin|attendance'],
    ],
    function() {
        Route::apiResource('recovery', 'SalesRecoveryApiController')->only('index')
             ->names('api.recovery');

        Route::get('recovery/getrecoverydata', 'SalesRecoveryApiController@getRecoveryData');
        Route::get('checkout/getrecoverydata', 'SalesRecoveryApiController@getRecoveryData');
        Route::get('recovery/getrefusedcart', 'SalesRecoveryApiController@getCartRefused');
        Route::get('recovery/getboleto', 'SalesRecoveryApiController@getBoletoOverdue');

        Route::post('recovery/details', 'SalesRecoveryApiController@getDetails');
        Route::post('recovery/regenerateboleto', 'SalesRecoveryApiController@regenerateBoleto');
    }
);
