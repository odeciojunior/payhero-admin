<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
    ],
    function() {
        //        project/{projectId}/plan
        Route::apiResource('/project/{projectId}/plans', 'PlansApiController')
             ->only('index', 'show', 'store', 'update', 'destroy')->names('api.plans');

        Route::get('/plans/user-plans', 'PlansApiController@getPlans')
             ->middleware('role:account_owner|admin|attendance');

        Route::post('/plans/update-bulk-cost', 'PlansApiController@updateBulkCost')
             ->middleware('role:account_owner|admin|attendance');

        Route::post('/plans/update-config-cost', 'PlansApiController@updateConfigCost')
             ->middleware('role:account_owner|admin|attendance');
    }
);
