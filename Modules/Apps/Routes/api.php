<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
    ],
    function() {
        Route::apiResource('apps', 'AppsApiController')
            ->only('index')->middleware('permission:apps');
    }

);

