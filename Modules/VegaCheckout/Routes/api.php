<?php

Route::middleware('auth:api')->get('/vegacheckout', function (Request $request) {
    return $request->user();
});

Route::group(["middleware" => ["auth:api", "permission:apps", "demo_account"]], function () {
    Route::get("apps/vegacheckout", "VegaCheckoutApiController@index");
    Route::get("apps/vegacheckout/{id}", "VegaCheckoutApiController@show");
    Route::get("apps/vegacheckout/{id}/edit", "VegaCheckoutApiController@edit");
    Route::post("apps/vegacheckout/", "VegaCheckoutApiController@store");
    Route::delete("apps/vegacheckout/{id}", "VegaCheckoutApiController@destroy")->name("vegacheckout.destroy");
});
