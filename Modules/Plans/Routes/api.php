<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'setUserAsLogged'],
    ],
    function() {
//        project/{projectId}/plan
        Route::apiResource('/project/{projectId}/plans', 'PlansApiController')
             ->only('index', 'show', 'store', 'update', 'destroy')->names('api.plans');
    }
);
