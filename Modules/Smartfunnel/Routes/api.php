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

// Route::middleware('auth:api')->get('/smartfunnel', function (Request $request) {
//     return $request->user();
// });

Route::group(["middleware" => ["auth:api", "permission:apps", "demo_account"]], function () {
    Route::get("apps/smartfunnel", "SmartfunnelApiController@index");
    Route::get("apps/smartfunnel/{id}", "SmartfunnelApiController@show");
    Route::get("apps/smartfunnel/{id}/edit", "SmartfunnelApiController@edit");

    Route::apiResource("apps/smartfunnel", "SmartfunnelApiController")
        ->only("create", "store", "update", "destroy")
        ->names("api.smartfunnel_api")
        ->middleware("permission:apps_manage");
});
