<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'role:account_owner|admin|attendance'],
        'prefix'     => 'sales',
    ],
    function() {

        Route::get('/filters', [
            'uses' => 'SalesApiController@filters',
        ]);

        Route::post('/export', [
            'as'   => 'sales.export',
            'uses' => 'SalesApiController@export',
        ]);

        Route::get('/resume', [
            'as'   => 'sales.resume',
            'uses' => 'SalesApiController@resume',
        ]);
    }
);

Route::apiResource('sales', 'SalesApiController')
     ->only('index', 'show')
     ->middleware(['auth:api']);
