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

Route::middleware(["web", "auth"])
    ->prefix("withdrawals")
    ->group(function () {
        Route::get("/download/{filename}", "WithdrawalsController@download")->middleware("permission:finances_manage");
    });
