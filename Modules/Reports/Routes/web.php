<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(["web", "auth"])
    ->prefix("reports")
    ->group(function () {
        Route::get("/", "ReportsController@resume")
            ->name("reports.resume")
            ->middleware("permission:reports");
        Route::get("/sales", "ReportsController@sales")
            ->name("reports.sales")
            ->middleware("permission:reports");
        Route::get("/finances", "ReportsController@finances")
            ->name("reports.finances")
            ->middleware("permission:reports");
        Route::get("/marketing", "ReportsController@marketing")
            ->name("reports.marketing")
            ->middleware("permission:reports");
        Route::get("/getValues/{project_id}", "ReportsController@getValues")
            ->name("reports.values")
            ->middleware("role:account_owner|admin");
        Route::get("/getsalesbyorigin", "ReportsController@getSalesByOrigin")
            ->name("reports.salesbyorigin")
            ->middleware("role:account_owner|admin");

        Route::get("/projections", "ReportsController@projections")
            ->name("reports.projections")
            ->middleware("role:account_owner|admin");
        Route::get("/pending", "ReportsController@pending")
            ->name("reports.pending")
            ->middleware("permission:reports");

        Route::get("/coupons", "ReportsController@coupons")
            ->name("reports.coupons")
            ->middleware("permission:reports");

        Route::get("/blockedbalance", "ReportsController@blockedbalance")
            ->name("reports.blockedbalance")
            ->middleware("permission:reports");
    });
