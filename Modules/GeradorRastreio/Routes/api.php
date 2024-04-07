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

Route::middleware('auth:api')->get('/geradorrastreio', function (Request $request) {
    return $request->user();
});

Route::middleware("auth:api")->get("/geradorrastreio", function (Request $request) {
    return $request->user();
});

Route::group(["middleware" => ["auth:api", "permission:apps", "demo_account"]], function () {
    Route::get("apps/geradorrastreio", "GeradorRastreioApiController@index");
    Route::get("apps/geradorrastreio/{id}", "GeradorRastreioApiController@show");
    Route::get("apps/geradorrastreio/{id}/edit", "GeradorRastreioApiController@edit");

    Route::apiResource("apps/geradorrastreio", "GeradorRastreioApiController")
        ->only("create", "store", "update", "destroy")
        ->middleware("permission:apps_manage");
});