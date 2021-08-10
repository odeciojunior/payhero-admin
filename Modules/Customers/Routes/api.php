<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
    ],
    function() {
        // Route::apiResource('/customers', 'CustomersApiController')->only('show', 'update')->names('api.customer')
        //  ->middleware('permission:sales');
        Route::get('/customers/{id}','CustomersApiController@show');
        Route::put('/customers/update','CustomersApiController@update')->middleware('permission:sales_manage|trackings_manage');
    }
);
