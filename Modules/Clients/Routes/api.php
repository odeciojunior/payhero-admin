<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth'],
    ],
    function() {
        Route::apiResource('/client', 'ClientApiController')->only('show')->names('api.client');
    }
);
