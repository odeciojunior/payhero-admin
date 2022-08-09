<?php

use Illuminate\Support\Facades\Route;
//role:account_owner|admin
Route::group(
    [
        "middleware" => ["web", "auth", "permission:products"],
    ],
    function () {
        Route::resource("/products", "ProductsController")->only("index", "edit");
        Route::get("/products/create/{type}", "ProductsController@create")->middleware("permission:products_manage");
    }
);
