<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api'],
    ],
    function() {
        Route::apiResource('/delivery', 'DeliveryApiController')->only('show')->names('api.client');
    }
);
