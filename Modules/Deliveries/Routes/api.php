<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin"],
    ],
    function () {
        Route::apiResource("/delivery", "DeliveryApiController")
            ->only("show")
            ->names("api.client");
    }
);
