<?php

Route::group(["middleware" => ["web", "auth", "permission:apps"]], function () {
    Route::get("apps/adooreicheckout", "AdooreiCheckoutController@index");
});