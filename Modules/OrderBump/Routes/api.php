<?php

use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:api", "scopes:admin","demo_account"]], function () {
    Route::get("orderbump", "OrderBumpApiController@index");
    Route::get("orderbump/{id}", "OrderBumpApiController@show");

    Route::post("orderbump", "OrderBumpApiController@store")->middleware("permission:projects_manage");
    Route::put("orderbump/{id}", "OrderBumpApiController@update")->middleware("permission:projects_manage");
    Route::delete("orderbump/{id}", "OrderBumpApiController@destroy")->middleware("permission:projects_manage");
});
