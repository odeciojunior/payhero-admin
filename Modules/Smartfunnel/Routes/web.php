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

// Route::prefix('smartfunnel')->group(function() {
//     Route::get('/', 'SmartfunnelController@index');
// });

Route::group(["middleware" => ["web", "auth", "permission:apps"]], function () {
    Route::get("apps/smartfunnel", "SmartfunnelController@index");
    Route::get("apps/smartfunnel/{id}", "SmartfunnelController@show");
    Route::get("apps/smartfunnel/{id}/edit", "SmartfunnelController@edit");

    Route::Resource("apps/smartfunnel", "SmartfunnelController")
        ->only("create", "store", "update", "destroy")
        ->names("smartfunnel")
        ->middleware("permission:apps_manage");
});
