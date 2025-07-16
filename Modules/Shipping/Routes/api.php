<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin", "demo_account"],
    ],
    function () {
        Route::get("/project/{projectId}/shippings", "ShippingApiController@index");
        Route::get("/project/{projectId}/shippings/{id}", "ShippingApiController@show");
        Route::get("/project/{projectId}/shippings/{id}/edit", "ShippingApiController@edit");

        Route::apiResource("/project/{projectId}/shippings", "ShippingApiController")
            ->only("store", "update", "destroy")
            ->names("api.shippings_api")
            ->middleware("permission:projects_manage");

        Route::get("/shippings/user-shippings", "ShippingApiController@getShippings");
    }
);
