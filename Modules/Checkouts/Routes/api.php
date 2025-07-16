<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin", "demo_account"],
    ],
    function () {
        Route::apiResource("checkout", "CheckoutApiController")
            ->only("index", "show")
            ->names("api.checkout_api");
    }
);
