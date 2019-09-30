<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api'],
    ],
    function() {
        Route::apiResource('reports', 'ReportsApiController')->only('index');

        Route::get('reports/getsalesbyorigin', 'ReportsApiController@getSalesByOrigin');
    }
);
