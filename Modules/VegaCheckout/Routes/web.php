<?php

Route::group(["middleware" => ["web", "auth", "permission:apps"]], function () {
    Route::get("apps/vegacheckout", "VegaCheckoutController@index");
    Route::get("apps/vegacheckout/{id}", "VegaCheckoutController@show");
    Route::get("apps/vegacheckout/{id}/edit", "VegaCheckoutController@edit");
    Route::post("apps/vegacheckout/", "VegaCheckoutApiController@store");
    Route::delete("apps/vegacheckout/{id}", "VegaCheckoutApiController@destroy")->name("vegacheckout.destroy");
});