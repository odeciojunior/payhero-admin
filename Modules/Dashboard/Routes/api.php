<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth'],
    ],
    function() {
        Route::apiResource('dashboard', 'DashboardApiController')
            ->only('index')
            ->names('api.dashboard');

        Route::post('/dashboard/getvalues', 'DashboardApiController@getValues');
    }
);
