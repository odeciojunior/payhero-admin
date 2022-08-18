<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin", "demo_account"],
    ],
    function () {
        // Reports - finances
        Route::get("/reports/resume/commissions", "ReportsFinanceApiController@getResumeCommissions");
        Route::get("/reports/resume/pendings", "ReportsFinanceApiController@getResumePendings");
        Route::get("/reports/resume/cashbacks", "ReportsFinanceApiController@getResumeCashbacks");

        Route::get("/reports/finances/resume", "ReportsFinanceApiController@getFinancesResume");
        Route::get("/reports/finances/cashbacks", "ReportsFinanceApiController@getFinancesCashbacks");
        Route::get("/reports/finances/pendings", "ReportsFinanceApiController@getFinancesPendings");
        Route::get("/reports/finances/blockeds", "ReportsFinanceApiController@getFinancesBlockeds");
        Route::get("/reports/finances/distribuitions", "ReportsFinanceApiController@getFinancesDistribuitions");
        Route::get("/reports/finances/withdrawals", "ReportsFinanceApiController@getFinancesWithdrawals");

        // Reports - sales
        Route::get("/reports/resume/sales", "ReportsSaleApiController@getResumeSales");
        Route::get("/reports/resume/type-payments", "ReportsSaleApiController@getResumeTypePayments");
        Route::get("/reports/resume/products", "ReportsSaleApiController@getResumeProducts");

        Route::get("/reports/sales/resume", "ReportsSaleApiController@getSalesResume");
        Route::get("/reports/sales/distribuitions", "ReportsSaleApiController@getSalesDistribuitions");
        Route::get("/reports/sales/abandoned-carts", "ReportsSaleApiController@getAbandonedCarts");
        Route::get("/reports/sales/orderbump", "ReportsSaleApiController@getOrderBump");
        Route::get("/reports/sales/upsell", "ReportsSaleApiController@getUpsell");
        Route::get("/reports/sales/conversion", "ReportsSaleApiController@getConversion");
        Route::get("/reports/sales/recurrence", "ReportsSaleApiController@getRecurrence");

        // Reports - marketing
        Route::get("/reports/resume/coupons", "ReportsMarketingApiController@getResumeCoupons");
        Route::get("/reports/resume/regions", "ReportsMarketingApiController@getResumeRegions");
        Route::get("/reports/resume/origins", "ReportsMarketingApiController@getResumeOrigins");

        Route::get("/reports/marketing/resume", "ReportsMarketingApiController@getResume");
        Route::get("/reports/marketing/sales-by-state", "ReportsMarketingApiController@getSalesByState");
        Route::get("/reports/marketing/most-frequent-sales", "ReportsMarketingApiController@getMostFrequentSales");
        Route::get("/reports/marketing/devices", "ReportsMarketingApiController@getDevices");
        Route::get("/reports/marketing/operational-systems", "ReportsMarketingApiController@getOperationalSystems");
        Route::get("/reports/marketing/state-details", "ReportsMarketingApiController@getStateDetail");
        Route::get("/reports/marketing/coupons", "ReportsMarketingApiController@getResumeCoupons");
        Route::get("/reports/marketing/regions", "ReportsMarketingApiController@getResumeRegions");
        Route::get("/reports/marketing/origins", "ReportsMarketingApiController@getResumeOrigins");

        Route::get("/reports/coupons", "ReportsApiController@getDiscountCoupons");
        Route::get("/reports/pending-balance", "ReportsApiController@pendingBalance");
        Route::get("/reports/resume-pending-balance", "ReportsApiController@resumePendingBalance");
        Route::get("/reports/blocked-balance", "ReportsApiController@blockedBalance");
        Route::get("/reports/resume-blocked-balance", "ReportsApiController@resumeblockedBalance");
        Route::get("/reports/block-reasons", "ReportsApiController@getBlockReasons");

        Route::get('/reports/projects-with-blocked-balance', 'ReportsApiController@getProjectsWithBlockedBalance')->middleware('permission:report_blockedbalance');
        Route::get('/reports/projects-with-checkouts', 'ReportsApiController@getProjectsWithCheckouts')->middleware('permission:report_checkouts');
        Route::get('/reports/projects-with-coupons', 'ReportsApiController@getProjectsWithCoupons')->middleware('permission:report_coupons');
        Route::get('/reports/projects-with-pending-balance', 'ReportsApiController@getProjectsWithPendingBalance')->middleware('permission:report_pending');
    }
);
