<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin", "demo_account"],
    ],
    function () {
        Route::get("/apps/nuvemshop", "NuvemshopApiController@index");
        Route::post("/apps/nuvemshop", "NuvemshopApiController@store");
        Route::post("/apps/nuvemshop/finalize", "NuvemshopApiController@finalizeIntegration");
        Route::post("/apps/nuvemshop/sync/products", "NuvemshopApiController@syncProducts");
        Route::post("/apps/nuvemshop/sync/trackings", "NuvemshopApiController@syncTrackings");
    },
);
