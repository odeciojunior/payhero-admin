<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['api', 'auth:api'],
    ],
    function() {
        Route::apiResource('checkout', 'CheckoutApiController')->only('index')->names('api.checkout');
    }
);
