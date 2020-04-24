<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin', 'setUserAsLogged'],
    ],
    function() {
        Route::apiResource('/project/{projectId}/shippings', 'ShippingApiController')
            ->only('index', 'store', 'update', 'destroy', 'show', 'edit');
    }
);
