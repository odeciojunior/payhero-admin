<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth'],
    ],
    function() {
        Route::apiResource('recovery', 'SalesRecoveryApiController')->only('index')
             ->names('api.recovery');

        Route::get('recovery/getrecoverydata', 'SalesRecoveryApiController@getRecoveryData');

        Route::post('recovery/details', 'SalesRecoveryApiController@getDetails');
    }
);
