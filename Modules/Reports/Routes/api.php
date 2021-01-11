<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
    ],
    function() {
        Route::apiResource('reports', 'ReportsApiController')->only('index')->middleware('role:account_owner|admin');

        Route::get('/reports/checkouts', 'ReportsApiController@checkouts')->middleware('role:account_owner|admin');

        Route::get('reports/getsalesbyorigin', 'ReportsApiController@getSalesByOrigin')->middleware('role:account_owner|admin');

        Route::get('reports/getcheckoutsbyorigin', 'ReportsApiController@getCheckoutsByOrigin')->middleware('role:account_owner|admin');

        Route::get('/reports/projections', 'ReportsApiController@projections')->middleware('role:account_owner|admin');

        Route::post('/reports/projectionsexport', 'ReportsApiController@projectionsExport')->middleware('role:account_owner|admin');

        Route::get('/reports/coupons', 'ReportsApiController@coupons')->middleware('role:account_owner|admin');

        Route::get('/reports/pending-balance', 'ReportsApiController@pendingBalance')->middleware('role:account_owner|admin');

        Route::get('/reports/resume-pending-balance', 'ReportsApiController@resumePendingBalance')->middleware('role:account_owner|admin');

        Route::get('/reports/blockedbalance', 'ReportsApiController@blockedbalance')->middleware('role:account_owner|admin');

        Route::get('/reports/blockedresume', 'ReportsApiController@resumeBlockedBalance')->middleware('role:account_owner|admin');

    }
);
