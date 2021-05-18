<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
    ],
    function() {
        Route::apiResource('/customers', 'CustomersApiController')->only('show', 'update')->names('api.customer');
    }
);
