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

Route::group(
    [
        "middleware" => ["web", "auth"],
    ],
    function () {
        Route::Resource("/projectreviewsconfig", "ProjectReviewsConfigApiController")
            ->only("index", "create", "edit")
            ->middleware("role:account_owner|admin");
    }
);
