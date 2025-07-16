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
        Route::get("/projectreviewsconfig", "ProjectReviewsConfigApiController@index");
        Route::get("/projectreviewsconfig/{id}", "ProjectReviewsConfigApiController@show");
        Route::get("/projectreviewsconfig/{id}/edit", "ProjectReviewsConfigApiController@edit");

        Route::apiResource("/projectreviewsconfig", "ProjectReviewsConfigApiController")
            ->only("store", "destroy", "update")
            ->names("api.projectreviewsconfig_api")
            ->middleware("permission:projects_manage");
    }
);
