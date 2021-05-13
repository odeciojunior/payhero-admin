<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
    ],
    function() {
        Route::apiResource('/apps/hotzapp', 'HotZappApiController')
            ->only('index', 'show', 'store', 'edit', 'update', 'destroy');
    }
);


