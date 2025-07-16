<?php

use Illuminate\Support\Facades\Route;

//role:account_owner|admin|finantial
Route::group(
    [
        "middleware" => ["auth:api", "demo_account", "permission:dashboard", "scopes:admin"],
    ],
    function () {
        Route::apiResource("dashboard", "DashboardApiController")
            ->only("index")
            ->names("api.dashboard_api");

        Route::post("/dashboard/getvalues", "DashboardApiController@getValues");
        Route::get("/dashboard/get-releases", "DashboardApiController@getReleases");
        Route::get("/dashboard/get-chart-data", "DashboardApiController@getChartData");
        Route::get("/dashboard/verifypendingdata", "DashboardApiController@verifyPendingData");

        Route::get("/dashboard/get-performance", "DashboardApiController@getPerformance");
        Route::get("/dashboard/get-account-health", "DashboardApiController@getAccountHealth");
        Route::get("/dashboard/get-account-chargeback", "DashboardApiController@getAccountChargeback");
        Route::get("/dashboard/get-account-attendance", "DashboardApiController@getAccountAttendance");
        Route::get("/dashboard/get-account-tracking", "DashboardApiController@getAccountTracking");
        Route::get("/dashboard/verify-achievements", "DashboardApiController@getAchievements");
        Route::put("/dashboard/update-achievements/{achievement}", "DashboardApiController@updateAchievements");
        Route::get("/dashboard/verify-pix-onboarding", "DashboardApiController@verifyPixOnboarding");
        Route::put("/dashboard/update-pix-onboarding/{onboarding}", "DashboardApiController@updatePixOnboarding");
        Route::get("/dashboard/verify-user-terms", "DashboardApiController@getUserTerms");
        Route::put("/dashboard/update-user-terms", "DashboardApiController@updateUserTerms");
    }
);
