<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api'],
    ],
    function() {
        Route::apiResource('/customer', 'ClientApiController')->only('show', 'update')->names('api.client');
    }
);
