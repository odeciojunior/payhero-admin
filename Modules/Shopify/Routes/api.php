<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin"],
    ],
    function () {
        Route::get("/apps/shopify", "ShopifyApiController@index");
        Route::post("/apps/shopify", "ShopifyApiController@store")->middleware("permission:apps_manage");

        Route::get("/apps/shopify/user-companies", [
            "uses" => "ShopifyApiController@getCompanies",
            "as" => "shopify.getcompanies",
        ]);

        Route::post("/apps/shopify/undointegration", [
            "uses" => "ShopifyApiController@undoIntegration",
            "as" => "shopify.undointegration",
        ]);

        Route::post("/apps/shopify/reintegration", [
            "uses" => "ShopifyApiController@reIntegration",
            "as" => "shopify.reintegration",
        ]);

        Route::post("/apps/shopify/synchronize/products", [
            "uses" => "ShopifyApiController@synchronizeProducts",
            "as" => "shopify.synchronize.product",
        ]);

        Route::post("/apps/shopify/synchronize/trackings", [
            "uses" => "ShopifyApiController@synchronizeTrackings",
            "as" => "shopify.synchronize.trackings",
        ])->middleware("throttle:60,1");

        Route::post("/apps/shopify/synchronize/templates", [
            "uses" => "ShopifyApiController@synchronizeTemplates",
            "as" => "shopify.synchronize.template",
        ]);

        Route::post("/apps/shopify/updatetoken", [
            "uses" => "ShopifyApiController@updateToken",
            "as" => "shopify.updatetoken",
        ]);

        Route::post("/apps/shopify/verifypermissions", [
            "uses" => "ShopifyApiController@verifyPermission",
            "as" => "shopify.verifypermissions",
        ]);

        Route::post("/apps/shopify/skiptocart", [
            "uses" => "ShopifyApiController@setSkipToCart",
            "as" => "shopify.skiptocart",
        ]);
    }
);
