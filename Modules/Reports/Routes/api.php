<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin', 'setUserAsLogged'],
    ],
    function() {
        Route::apiResource('reports', 'ReportsApiController')->only('index')->middleware('role:account_owner|admin');

        Route::get('/reports/checkouts', 'ReportsApiController@checkouts')->middleware('role:account_owner|admin');

        Route::get('reports/getsalesbyorigin', 'ReportsApiController@getSalesByOrigin')->middleware('role:account_owner|admin');

        Route::get('reports/getcheckoutsbyorigin', 'ReportsApiController@getCheckoutsByOrigin')->middleware('role:account_owner|admin');

        Route::get('/reports/projections', 'ReportsApiController@projections')->middleware('role:account_owner|admin');

        Route::post('/reports/projectionsexport', 'ReportsApiController@projectionsExport')->middleware('role:account_owner|admin');

        Route::get('/reports/coupons', 'ReportsApiController@coupons')->middleware('role:account_owner|admin');

    }
);
