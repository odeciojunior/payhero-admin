<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin', 'setUserAsLogged'],
    ],
    function() {
        Route::apiResource('apps', 'AppsApiController')
            ->only('index')->middleware('role:account_owner|admin');
    }

);

