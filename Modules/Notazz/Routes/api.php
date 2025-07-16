<?php

use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:api", "scopes:admin","demo_account"]], function () {
    Route::get("apps/notazz", "NotazzApiController@index");
    Route::get("apps/notazz/{id}", "NotazzApiController@show");
    Route::get("apps/notazz/{id}/edit", "NotazzApiController@edit");
    Route::apiResource("apps/notazz", "NotazzApiController")
        ->only("create", "store", "update", "destroy")
        ->names("api.notazz_api")
        ->middleware("permission:apps_manage");

    Route::get("apps/notazz/invoice/{id}", "NotazzApiController@getInvoice");

    Route::get("apps/notazz/report", "NotazzReportApiController@index");
    Route::get("apps/notazz/report/{id}", "NotazzReportApiController@show");
    Route::get("apps/notazz/report/{id}/edit", "NotazzReportApiController@edit");
    Route::apiResource("apps/notazz/report", "NotazzReportApiController")
        ->only("create", "store", "update", "destroy")
        ->names("api.notazzreport_api")
        ->middleware("permission:apps_manage");

    Route::get("apps/notazz/export/{id}/", "NotazzReportApiController@invoicesExport");
});
