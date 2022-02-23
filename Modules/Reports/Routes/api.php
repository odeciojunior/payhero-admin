<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
    ],
    function() {
        Route::get('reports', 'ReportsApiController@index')->middleware('permission:report_sales');
        Route::get('reports/getsalesbyorigin', 'ReportsApiController@getSalesByOrigin')->middleware('permission:report_sales');

        Route::get('/reports/checkouts', 'ReportsApiController@checkouts')->middleware('permission:report_checkouts');
        Route::get('reports/getcheckoutsbyorigin', 'ReportsApiController@getCheckoutsByOrigin')->middleware('permission:report_checkouts');

        Route::get('/reports/projections', 'ReportsApiController@projections')->middleware('role:account_owner|admin');

        Route::post('/reports/projectionsexport', 'ReportsApiController@projectionsExport')->middleware('role:account_owner|admin');

        Route::get('/reports/coupons', 'ReportsApiController@coupons')->middleware('permission:report_coupons');

        Route::get('/reports/pending-balance', 'ReportsApiController@pendingBalance')->middleware('permission:report_pending');

        Route::get('/reports/resume-pending-balance', 'ReportsApiController@resumePendingBalance')->middleware('permission:report_pending');

        Route::get('/reports/blockedbalance', 'ReportsApiController@blockedbalance')->middleware('permission:report_blockedbalance');

        Route::get('/reports/blockedresume', 'ReportsApiController@resumeBlockedBalance')->middleware('permission:report_blockedbalance');

        // Reports new
        Route::get('/reports/get-comission', 'ReportsApiController@getComission');
        Route::get('/reports/get-pending', 'ReportsApiController@getPendings');
        Route::get('/reports/get-cashback', 'ReportsApiController@getCashbacks');

        Route::get('/reports/get-sales', 'ReportsApiController@getSales');
        Route::get('/reports/get-type-payments', 'ReportsApiController@getTypePayments');
        Route::get('/reports/get-products', 'ReportsApiController@getProducts');

        Route::get('/reports/get-coupons', 'ReportsApiController@getCoupons');
        Route::get('/reports/get-regions', 'ReportsApiController@getRegions');
        Route::get('/reports/get-origins', 'ReportsApiController@getOrigins');
    }
);
