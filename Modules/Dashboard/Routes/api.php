<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'role:account_owner|admin', 'setUserAsLogged'],
    ],
    function() {
        Route::apiResource('dashboard', 'DashboardApiController')
             ->only('index')
             ->names('api.dashboard');

        Route::post('/dashboard/getvalues', 'DashboardApiController@getValues');
    }
);
