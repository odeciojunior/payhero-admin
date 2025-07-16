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
    [
        "middleware" => ["auth:api", "scopes:admin", "permission:apps","demo_account"],
    ],
    function () {
        Route::get("/apps/hotbillet", "HotBilletApiController@index");
        Route::get("/apps/hotbillet/{id}", "HotBilletApiController@show");
        Route::get("/apps/hotbillet/{id}/edit", "HotBilletApiController@edit");

        Route::apiResource("/apps/hotbillet", "HotBilletApiController")
            ->only("store", "update", "destroy")
            ->names("api.hotbillet_api")
            ->middleware("permission:apps_manage");
    }
);
