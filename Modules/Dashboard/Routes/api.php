<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api'],
    ],
    function() {
        Route::post('/dashboard/resume', 'DashboardApiController@resume')->name('api.dashboard.resume');
        //        Route::apiResource('dashboard', 'DashboardApiController')
        //            ->only('index')
        //            ->names('api.dashboard');
        //
        //        Route::post('/dashboard/getvalues', 'DashboardApiController@getValues');
    }
);
