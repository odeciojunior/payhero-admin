<?php

use Illuminate\Support\Facades\Route;

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

Route::group(["middleware" => ["auth:api", "scopes:admin", "permission:apps","demo_account"]], function () {
    Route::get("integrations", "IntegrationsApiController@index");
    Route::get("integrations/{id}", "IntegrationsApiController@show");
    Route::apiResource("integrations", "IntegrationsApiController")
        ->only("store", "destroy", "update")
        ->names("api.integrations")
        ->middleware("permission:apps_manage");

    Route::post("/integrations/{integration}/refreshtoken", "IntegrationsApiController@refreshToken")
        ->name("api.integrations.refreshtoken")
        ->middleware("permission:apps_manage");
});
