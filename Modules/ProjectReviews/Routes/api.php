<?php

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
        Route::get("/projectreviews", "ProjectReviewsApiController@index");
        Route::get("/projectreviews/{id}", "ProjectReviewsApiController@show");
        Route::get("/projectreviews/{id}/edit", "ProjectReviewsApiController@edit");

        Route::apiResource("/projectreviews", "ProjectReviewsApiController")
            ->only("store", "destroy", "update")
            ->names("api.projectreviews_api")
            ->middleware("permission:projects_manage");
    }
);
