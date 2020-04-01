<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'setUserAsLogged'],

    ],
    function() {
        Route::apiResource('terms/', 'UserTermsApiController')
             ->only('store')->names('api.userterms');
    }
);
