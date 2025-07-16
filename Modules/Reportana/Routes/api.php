<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware("auth:api")->get("/reportana", function (Request $request) {
    return $request->user();
});

Route::group(["middleware" => ["auth:api", "permission:apps", "demo_account"]], function () {
    Route::get("apps/reportana", "ReportanaApiController@index")->name("api.reportana.index");
    Route::get("apps/reportana/{id}", "ReportanaApiController@show")->name("api.reportana.show");
    Route::get("apps/reportana/{id}/edit", "ReportanaApiController@edit")->name("api.reportana.edit");

    Route::apiResource("apps/reportana", "ReportanaApiController")
        ->only("store", "update", "destroy")
        ->names([
            'store' => 'api.reportana_api.resource.store',
            'update' => 'api.reportana_api.resource.update',
            'destroy' => 'api.reportana_api.resource.destroy'
        ])
        ->middleware("permission:apps_manage");
});
