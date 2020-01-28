<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'setUserAsLogged'],
    ],
    function() {
        Route::apiResource('/delivery', 'DeliveryApiController')->only('show')->names('api.client');
    }
);
