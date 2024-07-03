<?php

Route::group(["middleware" => ["web", "auth", "permission:apps"]], function () {
    Route::get("apps/adooreicheckout", "AdooreiCheckoutApiController@index");
    Route::get("apps/adooreicheckout/{id}", "AdooreiCheckoutApiController@show");
    Route::get("apps/adooreicheckout/{id}/edit", "AdooreiCheckoutApiController@edit");
    Route::post("apps/adooreicheckout/", "AdooreiCheckoutApiController@store");
    Route::delete("apps/adooreicheckout/{id}", "AdooreiCheckoutApiController@destroy")->name("adooreicheckout.destroy");
});