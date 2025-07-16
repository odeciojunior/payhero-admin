<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin", "permission:apps", "demo_account"],
    ],
    function () {
        Route::apiResource("apps", "AppsApiController")
            ->only("index")
            ->names("api.apps_api");
    }
);
