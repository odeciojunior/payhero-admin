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
        Route::get("/projectupsellrule", "ProjectUpsellRuleApiController@index");
        Route::get("/projectupsellrule/{id}", "ProjectUpsellRuleApiController@show");
        Route::get("/projectupsellrule/{id}/edit", "ProjectUpsellRuleApiController@edit");

        //role:account_owner|admin
        Route::apiResource("/projectupsellrule", "ProjectUpsellRuleApiController")
            ->only("store", "destroy", "update")
            ->names("api.projectupsellrule_api")
            ->middleware("permission:projects_manage");
    }
);
//Route::middleware('auth:api')->get('/projectupsellrule', function (Request $request) {
//    return $request->user();
//});
