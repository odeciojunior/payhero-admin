<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(["middleware" => ["web", "auth", "permission:apps"]], function () {
    Route::get("apps/whatsapp2", "Whatsapp2Controller@index");
    Route::get("apps/whatsapp2/{id}", "Whatsapp2Controller@show");
    Route::get("apps/whatsapp2/{id}/edit", "Whatsapp2Controller@edit");

    Route::Resource("apps/whatsapp2", "Whatsapp2Controller")->only("create", "store", "update", "destroy");
});
