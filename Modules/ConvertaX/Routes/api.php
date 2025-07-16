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
        "middleware" => ["auth:api", "scopes:admin","demo_account"],
    ],
    function () {
        Route::get("/apps/convertax", "ConvertaXApiController@index");
        Route::get("/apps/convertax/{id}", "ConvertaXApiController@show");
        Route::apiResource("/apps/convertax", "ConvertaXApiController")
            ->only("store", "update", "destroy")
            ->names("api.convertax_api")
            ->middleware("permission:apps_manage");

        Route::get("/getconvertaxintegrations", "ConvertaXController@getIntegrations");
    }
);
