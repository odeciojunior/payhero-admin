<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["web", "auth", "role:account_owner|admin|attendance"],
    ],
    function () {
        Route::resource("antifraud", "SalesBlackListAntifraudController")
            ->only("index")
            ->names("antifraud");
    }
);
