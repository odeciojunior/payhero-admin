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
        "middleware" => ["auth:api", "scopes:admin","demo_account"],
    ],
    function () {
        Route::get("/project/{projectId}/projectnotification", "ProjectNotificationApiController@index");
        Route::get("/project/{projectId}/projectnotification/{id}", "ProjectNotificationApiController@show");
        Route::get("/project/{projectId}/projectnotification/{id}/edit", "ProjectNotificationApiController@edit");

        Route::apiResource("/project/{projectId}/projectnotification", "ProjectNotificationApiController")
            ->only("store", "update", "destroy")
            ->names("api.projectnotification_api")
            ->middleware("permission:projects_manage");
    }
);
