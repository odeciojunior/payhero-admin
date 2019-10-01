<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api'],
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
    }
);

Route::apiResource('sales', 'SalesApiController')
    ->only('index', 'show')
    ->middleware(['auth:api']);
