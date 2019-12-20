<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'setUserAsLogged'],
    ],
    function() {
        Route::apiResource('/project/{projectId}/pixels', 'PixelsApiController')
             ->only('index', 'store', 'update', 'destroy', 'show', 'edit');
    }
);
