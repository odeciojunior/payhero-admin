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

        Route::get('/reports/resume/coupons', 'ReportsMarketingApiController@getResumeCoupons');
        Route::get('/reports/resume/regions', 'ReportsMarketingApiController@getResumeRegions');
        Route::get('/reports/resume/origins', 'ReportsMarketingApiController@getResumeOrigins');

        // Reports - finances
        Route::get('/reports/finances/resume', 'ReportsFinanceApiController@getFinancesResume');
        Route::get('/reports/finances/cashbacks', 'ReportsFinanceApiController@getFinancesCashbacks');
        Route::get('/reports/finances/pendings', 'ReportsFinanceApiController@getFinancesPendings');
        Route::get('/reports/finances/blockeds', 'ReportsFinanceApiController@getFinancesBlockeds');
        Route::get('/reports/finances/distribuitions', 'ReportsFinanceApiController@getFinancesDistribuitions');
        Route::get('/reports/finances/withdrawals', 'ReportsFinanceApiController@getFinancesWithdrawals');

        // Reports - sales
        Route::get('/reports/sales/resume', 'ReportsSaleApiController@getSalesResume');
        Route::get('/reports/sales/distribuitions', 'ReportsSaleApiController@getSalesDistribuitions');

        Route::get('/reports/sales/abandoned-carts', 'ReportsSaleApiController@getAbandonedCarts');
        Route::get('/reports/sales/orderbump', 'ReportsSaleApiController@getOrderBump');
        Route::get('/reports/sales/upsell', 'ReportsSaleApiController@getUpsell');
        Route::get('/reports/sales/conversion', 'ReportsSaleApiController@getConversion');
        Route::get('/reports/sales/recurrence', 'ReportsSaleApiController@getRecurrence');

        // Reports - marketing
        Route::get('/reports/marketing/resume', 'ReportsMarketingApiController@getResume');
        Route::get('/reports/marketing/sales-by-state', 'ReportsMarketingApiController@getSalesByState');
        Route::get('/reports/marketing/most-frequent-sales', 'ReportsMarketingApiController@getMostFrequentSales');
        Route::get('/reports/marketing/devices', 'ReportsMarketingApiController@getDevices');
        Route::get('/reports/marketing/operational-systems', 'ReportsMarketingApiController@getOperationalSystems');
        Route::get('/reports/marketing/state-details', 'ReportsMarketingApiController@getStateDetail');
        Route::get('/reports/marketing/coupons', 'ReportsMarketingApiController@getResumeCoupons');
        Route::get('/reports/marketing/regions', 'ReportsMarketingApiController@getResumeRegions');
        Route::get('/reports/marketing/origins', 'ReportsMarketingApiController@getResumeOrigins');
    }
);
