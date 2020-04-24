<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin', 'setUserAsLogged'],

    ],
    function() {
        Route::apiResource('terms/', 'UserTermsApiController')
             ->only('store')->names('api.userterms');
    }
);
