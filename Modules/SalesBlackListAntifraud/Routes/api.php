<?php

use Illuminate\Support\Facades\Route;

Route::apiResource("antifraud", "SalesBlackListAntifraudApiController")
    ->only("index", "show")
    ->names("api.antifraud_api")
    ->middleware(["auth:api", "scopes:admin"]);
