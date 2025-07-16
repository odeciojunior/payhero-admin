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

Route::group(["middleware" => ["web", "auth", "permission:apps"]], function () {
    Route::get("apps/reportana", "ReportanaController@index")->name("reportana.index");
    Route::get("apps/reportana/{id}", "ReportanaController@show")->name("reportana.show");
    Route::get("apps/reportana/{id}/edit", "ReportanaController@edit")->name("reportana.edit");

    Route::Resource("apps/reportana", "ReportanaController")
        ->only("create", "store", "update", "destroy")
        ->names([
            'create' => 'reportana.resource.create',
            'store' => 'reportana.resource.store',
            'update' => 'reportana.resource.update',
            'destroy' => 'reportana.resource.destroy'
        ])
        ->middleware("permission:apps_manage");
});
