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

Route::group(["middleware" => ["auth:api", "scopes:admin", "permission:apps", "demo_account"]], function () {
    Route::get("apps/whatsapp2", "Whatsapp2ApiController@index");
    Route::get("apps/whatsapp2/{id}", "Whatsapp2ApiController@show");
    Route::get("apps/whatsapp2/{id}/edit", "Whatsapp2ApiController@edit");

    Route::apiResource("apps/whatsapp2", "Whatsapp2ApiController")
        ->only("create", "store", "update", "destroy")
        ->names("api.whatsapp2_api")
        ->middleware("permission:apps_manage");
});
