<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api'],
    ],
    function() {
        Route::apiResource('reports', 'ReportsApiController')->only('index')->middleware('role:account_owner|admin');

        Route::get('/reports/checkouts', 'ReportsApiController@checkouts')->middleware('role:account_owner|admin');
        
        Route::get('reports/getsalesbyorigin', 'ReportsApiController@getSalesByOrigin')->middleware('role:account_owner|admin');

        Route::get('reports/getcheckoutsbyorigin', 'ReportsApiController@getCheckoutsByOrigin')->middleware('role:account_owner|admin');
    }
);
