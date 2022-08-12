<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin", "permission:apps"],
    ],
    function () {
        Route::apiResource("apps", "AppsApiController")->only("index");
    }
);
