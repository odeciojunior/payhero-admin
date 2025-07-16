<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin","demo_account"],
    ],
    function () {
        Route::get("/projects/{projectId}/pixels/configs", "PixelsApiController@getPixelConfigs")->name(
            "pixels.getconfig"
        );
        Route::post("/projects/{projectId}/pixels/saveconfigs", "PixelsApiController@storePixelConfigs")
            ->name("pixels.saveconfig")
            ->middleware("permission:projects_manage");

        Route::get("/project/{projectId}/pixels", "PixelsApiController@index");
        Route::get("/project/{projectId}/pixels/{id}", "PixelsApiController@show");
        Route::get("/project/{projectId}/pixels/{id}/edit", "PixelsApiController@edit");

        Route::apiResource("/project/{projectId}/pixels", "PixelsApiController")
            ->only("update", "destroy", "store")
            ->names("api.pixels_api")
            ->middleware("permission:projects_manage");
    }
);
