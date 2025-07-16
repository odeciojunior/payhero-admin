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
        'middleware' => ['auth:api','demo_account', 'scopes:admin','permission:apps'],
    ],
    function () {
        Route::get("/apps/astronmembers", "AstronMembersApiController@index");
        Route::get("/apps/astronmembers/{id}", "AstronMembersApiController@show");
        Route::get("/apps/astronmembers/{id}/edit", "AstronMembersApiController@edit");

        Route::apiResource("/apps/astronmembers", "AstronMembersApiController")
            ->only("store", "update", "destroy")
            ->names("api.astronmembers_api")
            ->middleware("permission:apps_manage");
    }
);
