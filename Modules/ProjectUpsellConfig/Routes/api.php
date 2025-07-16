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
Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin", "demo_account"],
    ],
    function () {
        Route::get("/projectupsellconfig", "ProjectUpsellConfigApiController@index");
        Route::get("/projectupsellconfig/{id}", "ProjectUpsellConfigApiController@show");
        Route::get("/projectupsellconfig/{id}/edit", "ProjectUpsellConfigApiController@edit");

        Route::apiResource("/projectupsellconfig", "ProjectUpsellConfigApiController")
            ->only("store", "destroy", "update")
            ->names("api.projectupsellconfig_api")
            ->middleware("permission:projects_manage");
    }
);
//Route::middleware('auth:api')->get('/projectupsellconfig', function (Request $request) {
//    return $request->user();
//});
