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
    Route::get("apps/geradorrastreio", "GeradorRastreioController@index")->name("geradorrastreio.index");
    Route::get("apps/geradorrastreio/{id}", "GeradorRastreioController@show")->name("geradorrastreio.show");
    Route::get("apps/geradorrastreio/{id}/edit", "GeradorRastreioController@edit")->name("geradorrastreio.edit");

    Route::Resource("apps/geradorrastreio", "GeradorRastreioController")
        ->only("create", "store", "update", "destroy")
        ->names([
            'create' => 'geradorrastreio.resource.create',
            'store' => 'geradorrastreio.resource.store',
            'update' => 'geradorrastreio.resource.update',
            'destroy' => 'geradorrastreio.resource.destroy'
        ])
        ->middleware("permission:apps_manage");
});