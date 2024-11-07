<?php

use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["web", "auth", "permission:apps"]], function () {
    Route::get("apps/nuvemshop", "NuvemshopController@index");
    Route::get("apps/nuvemshop/finalize", "NuvemshopController@finalizeIntegration");
});
