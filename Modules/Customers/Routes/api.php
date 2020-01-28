<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api'],
    ],
    function() {
        Route::apiResource('/customers', 'CustomersApiController')->only('show', 'update')->names('api.customer');
    }
);
