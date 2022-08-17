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

Route::group(
    ["middleware" => ["auth:api", "scopes:admin", "permission:apps"]],
    function () {
        Route::get("webhooks", "WebhooksApiController@index");
        Route::get("webhooks/{id}", "WebhooksApiController@show");
        Route::apiResource("webhooks", "WebhooksApiController")
            ->only("store", "destroy", "update")
            ->names("api.webhooks")
            ->middleware("permission:apps_manage");
        Route::post(
            "/webhooks/{webhook}/refreshtoken",
            "WebhooksApiController@refreshToken"
        )
            ->name("api.webhooks.refreshtoken")
            ->middleware("permission:apps_manage");
    }
);
