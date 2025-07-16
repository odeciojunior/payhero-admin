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

Route::group(["middleware" => ["auth:api", "permission:apps", "demo_account"]], function () {
    Route::get("apps/geradorrastreio", "GeradorRastreioApiController@index")->name("api.geradorrastreio.index");
    Route::get("apps/geradorrastreio/{id}", "GeradorRastreioApiController@show")->name("api.geradorrastreio.show");
    Route::get("apps/geradorrastreio/{id}/edit", "GeradorRastreioApiController@edit")->name("api.geradorrastreio.edit");

    Route::apiResource("apps/geradorrastreio", "GeradorRastreioApiController")
        ->only("store", "update", "destroy")
        ->names([
            'store' => 'api.geradorrastreio_api.resource.store',
            'update' => 'api.geradorrastreio_api.resource.update',
            'destroy' => 'api.geradorrastreio_api.resource.destroy'
        ])
        ->middleware("permission:apps_manage");
});