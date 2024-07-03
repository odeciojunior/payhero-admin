<?php

Route::middleware('auth:api')->get('/adooreicheckout', function (Request $request) {
    return $request->user();
});

// Route::group(["middleware" => ["auth:api", "permission:apps", "demo_account"]], function () {
//     Route::get("apps/adooreicheckout", "AdooreiCheckoutApiController@index");
//     Route::get("apps/adooreicheckout/{id}", "AdooreiCheckoutApiController@show");
//     Route::get("apps/adooreicheckout/{id}/edit", "AdooreiCheckoutApiController@edit");
//     Route::post("apps/adooreicheckout/", "AdooreiCheckoutApiController@store");
//     Route::post("apps/adooreicheckout/{id}", "AdooreiCheckoutApiController@update");
//     Route::delete("apps/adooreicheckout/{id}", "AdooreiCheckoutApiController@destroy")->name("adooreicheckout.destroy");
// });

Route::group(["middleware" => ["auth:api", "permission:apps", "demo_account"]], function () {
    Route::get("apps/adooreicheckout", "AdooreiCheckoutApiController@index");
    Route::get("apps/adooreicheckout/{id}", "AdooreiCheckoutApiController@show");
    Route::get("apps/adooreicheckout/{id}/edit", "AdooreiCheckoutApiController@edit");

    Route::apiResource("apps/adooreicheckout", "AdooreiCheckoutApiController")
        ->only("create", "store", "update", "destroy")
        ->middleware("permission:apps_manage");
});