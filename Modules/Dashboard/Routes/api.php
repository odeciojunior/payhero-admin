<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api'],
    ],
    function() {
        Route::apiResource('dashboard', 'DashboardApiController')
             ->only('index')
             ->names('api.dashboard');

        Route::post('/dashboard/getvalues', 'DashboardApiController@getValues');
    }
);
