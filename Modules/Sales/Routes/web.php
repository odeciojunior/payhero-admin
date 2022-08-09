<?php

use Illuminate\Support\Facades\Route;
//role_or_permission:account_owner|admin|attendance|finantial|
Route::group(
    [
        "middleware" => ["web", "auth", "permission:sales"],
    ],
    function () {
        Route::resource("/sales", "SalesController")->only("index");
        Route::get("/sales/download/{filename}", "SalesController@download");
        Route::get("/sales/{id}/refundreceipt", "SalesController@refundReceipt");
    }
);
