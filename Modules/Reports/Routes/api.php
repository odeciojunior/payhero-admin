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

        // Reports Finances
        Route::get('/reports/finances/comission-balance', 'ReportsApiController@getComissionBalanceFinances');
        Route::get('/reports/finances/pending-balance', 'ReportsApiController@getPendingBalanceFinances');
        Route::get('/reports/finances/cashback-balance', 'ReportsApiController@getCashbackBalanceFinances');

        // Reports Sales
        Route::get('/reports/sales/total-sales', 'ReportsApiController@getTotalSales');
        Route::get('/reports/sales/payments-type', 'ReportsApiController@getPaymentsTypeSales');
        Route::get('/reports/sales/products-topselling', 'ReportsApiController@getProductsTopsellingSales');

        // Reports Marketing
        Route::get('/reports/marketing/coupons', 'ReportsApiController@getCouponsMarketing');
        Route::get('/reports/marketing/regions', 'ReportsApiController@getRegionsMarketing');
        Route::get('/reports/marketing/origins', 'ReportsApiController@getOriginsMarketing');
    }
);
