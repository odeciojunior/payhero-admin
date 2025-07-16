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

Route::middleware("auth:api")->get("/unicodrop", function (Request $request) {
    return $request->user();
});

Route::group(["middleware" => ["auth:api", "demo_account"]], function () {
    Route::apiResource("apps/unicodrop", "UnicodropApiController")
        ->only("index", "create", "store", "edit", "update", "show", "destroy")
        ->names("api.unicodrop_api");
});
