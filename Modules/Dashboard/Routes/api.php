<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'role:account_owner|admin', 'scopes:admin', 'setUserAsLogged'],
    ],
    function() {
        Route::apiResource('dashboard', 'DashboardApiController')
             ->only('index')
             ->names('api.dashboard');

        Route::post('/dashboard/getvalues', 'DashboardApiController@getValues');
        Route::get('/dashboard/get-releases', 'DashboardApiController@getReleases');
        Route::get('/dashboard/verifypendingdata', 'DashboardApiController@verifyPendingData');
    }
);
