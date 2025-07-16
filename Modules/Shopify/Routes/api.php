<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin", "demo_account"],
    ],
    function () {
        Route::get("/apps/shopify", "ShopifyApiController@index");
        Route::post("/apps/shopify", "ShopifyApiController@store")->middleware("permission:apps_manage");

        Route::get("/apps/shopify/user-companies", [
            "uses" => "ShopifyApiController@getCompanies",
            "as" => "shopify.getcompanies_api",
        ]);

        Route::post("/apps/shopify/undointegration", [
            "uses" => "ShopifyApiController@undoIntegration",
            "as" => "shopify.undointegration_api",
        ]);

        Route::post("/apps/shopify/reintegration", [
            "uses" => "ShopifyApiController@reIntegration",
            "as" => "shopify.reintegration_api",
        ]);

        Route::post("/apps/shopify/synchronize/products", [
            "uses" => "ShopifyApiController@synchronizeProducts",
            "as" => "shopify.synchronize.product_api",
        ]);

        Route::post("/apps/shopify/synchronize/trackings", [
            "uses" => "ShopifyApiController@synchronizeTrackings",
            "as" => "shopify.synchronize.trackings_api",
        ])->middleware("throttle:60,1");

        Route::post("/apps/shopify/synchronize/templates", [
            "uses" => "ShopifyApiController@synchronizeTemplates",
            "as" => "shopify.synchronize.template_api",
        ]);

        Route::post("/apps/shopify/updatetoken", [
            "uses" => "ShopifyApiController@updateToken",
            "as" => "shopify.updatetoken_api",
        ]);

        Route::delete("/apps/shopify/delete", [
            "uses" => "ShopifyApiController@destroy",
            "as" => "shopify.delete_api",
        ]);

        Route::post("/apps/shopify/verifypermissions", [
            "uses" => "ShopifyApiController@verifyPermission",
            "as" => "shopify.verifypermissions_api",
        ]);

        Route::post("/apps/shopify/skiptocart", [
            "uses" => "ShopifyApiController@setSkipToCart",
            "as" => "shopify.skiptocart_api",
        ]);
    }
);
