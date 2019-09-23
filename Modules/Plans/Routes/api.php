<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth'],
    ],
    function() {
        Route::apiResource('/plans', 'PlansApiController')
             ->only('index', 'show', 'store', 'update', 'destroy');
    }
);
