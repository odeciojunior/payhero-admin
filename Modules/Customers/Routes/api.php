<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin","demo_account"],
    ],
    function () {
        Route::get("/customers/{id}", "CustomersApiController@show");
        Route::put("/customers/update", "CustomersApiController@update")->middleware("permission:sales_manage");
        Route::get("/customers/{id}/{sale_id}", "CustomersApiController@show");
    }
);
