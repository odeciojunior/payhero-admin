<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin", "permission:apps","demo_account"],
    ],
    function () {
        Route::get("/apps/hotzapp", "HotZappApiController@index");
        Route::get("/apps/hotzapp/{id}", "HotZappApiController@show");
        Route::get("/apps/hotzapp/{id}/edit", "HotZappApiController@edit");

        Route::apiResource("/apps/hotzapp", "HotZappApiController")
            ->only("store", "update", "destroy")
            ->names("api.hotzapp_api")
            ->middleware("permission:apps_manage");
    }
);
