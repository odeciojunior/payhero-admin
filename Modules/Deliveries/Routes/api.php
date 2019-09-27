<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth'],
    ],
    function() {
        Route::apiResource('/sale/{sale}/delivery', 'DeliveryApiController')->only('show')->names('api.client');
    }
);
