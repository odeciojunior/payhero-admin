<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin","demo_account"],
    ],
    function () {
        Route::get("/project/{projectId}/couponsdiscounts", "DiscountCouponsApiController@index");
        Route::get("/project/{projectId}/couponsdiscounts/{id}", "DiscountCouponsApiController@show");
        Route::get("/project/{projectId}/couponsdiscounts/{id}/edit", "DiscountCouponsApiController@edit");

        Route::apiResource("/project/{projectId}/couponsdiscounts", "DiscountCouponsApiController")
            ->only("index", "store", "update", "destroy", "show", "edit")
            ->names("api.couponsdiscounts_api");

        Route::put("/project/{projectId}/discounts/{id}", "DiscountCouponsApiController@update");
    }
);
