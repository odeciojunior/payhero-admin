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

        // Reports - resume
        Route::get('/reports/resume/commissions', 'ReportsApiController@getResumeCommissions');
        Route::get('/reports/resume/pendings', 'ReportsApiController@getResumePendings');
        Route::get('/reports/resume/cashbacks', 'ReportsApiController@getResumeCashbacks');

        Route::get('/reports/resume/sales', 'ReportsApiController@getResumeSales');
        Route::get('/reports/resume/type-payments', 'ReportsApiController@getResumeTypePayments');
        Route::get('/reports/resume/products', 'ReportsApiController@getResumeProducts');

        Route::get('/reports/resume/coupons', 'ReportsApiController@getResumeCoupons');
        Route::get('/reports/resume/regions', 'ReportsApiController@getResumeRegions');
        Route::get('/reports/resume/origins', 'ReportsApiController@getResumeOrigins');

        // Reports - finances
        Route::get('/reports/finances/resume', 'ReportsApiController@getFinancesResume');
        Route::get('/reports/finances/cashbacks', 'ReportsApiController@getFinancesCashbacks');
        Route::get('/reports/finances/pendings', 'ReportsApiController@getFinancesPendings');

        // Reports - sales

        // Reports - marketing
        Route::get('/reports/marketing/resume', 'ReportsApiController@getResume');
        Route::get('/reports/marketing/sales-by-state', 'ReportsApiController@getSalesByState');
        Route::get('/reports/marketing/most-frequent-sales', 'ReportsApiController@getMostFrequentSales');
        Route::get('/reports/marketing/devices', 'ReportsApiController@getDevices');
    }
);
