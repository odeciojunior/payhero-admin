<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin","demo_account"],
    ],
    function () {
        Route::apiResource("/delivery", "DeliveryApiController")
            ->only("show")
            ->names("api.client_api");
    }
);
