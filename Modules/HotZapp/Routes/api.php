<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'setUserAsLogged'],
    ],
    function() {
        Route::apiResource('/apps/hotzapp', 'HotZappApiController')
            ->only('index', 'show', 'store', 'edit', 'update', 'destroy');
    }
);


