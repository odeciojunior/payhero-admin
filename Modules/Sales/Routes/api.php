<?php

use Illuminate\Support\Facades\Route;
//'role:account_owner|admin|attendance|finantial'
Route::group(
    [
        "middleware" => ["auth:api", "permission:sales|contestations|trackings|finances", "scopes:admin"],
        "prefix" => "sales",
    ],
    function () {
        Route::get("/filters", [
            "uses" => "SalesApiController@filters",
        ]);

        Route::post("/export", [
            "as" => "sales.export",
            "uses" => "SalesApiController@export",
        ]);

        Route::get("/resume", [
            "as" => "sales.resume",
            "uses" => "SalesApiController@resume",
        ]);
        Route::post("/refund/{transaction_id}", "SalesApiController@refund")->middleware([
            "permission:sales_manage",
            "permission:finances_manage",
        ]);
        Route::post("/newordershopify/{transaction_id}", "SalesApiController@newOrderShopify")->middleware(
            "permission:sales_manage"
        );
        Route::post("/neworderwoocommerce/{transaction_id}", "SalesApiController@newOrderWoocommerce")->middleware(
            "permission:sales_manage"
        );
        Route::post(
            "/updaterefundobservation/{transaction_id}",
            "SalesApiController@updateRefundObservation"
        )->middleware("permission:sales_manage");
        Route::post("/saleresendemail", "SalesApiController@saleReSendEmail")->middleware("permission:sales_manage");
        Route::get("/user-plans", "SalesApiController@getPlans");
        Route::post("/set-observation/{transaction_id}", "SalesApiController@setValueObservation")->middleware(
            "permission:sales_manage"
        );
    }
);

Route::apiResource("sales", "SalesApiController")
    ->only("index", "show")
    ->middleware(["auth:api", "scopes:admin"]);

//rotas consumida por terceiros: profitfy
Route::group(["middleware" => ["auth:api", "scopes:sale", "throttle:120,1"], "prefix" => "profitfy"], function () {
    Route::get("/orders", "ProfitfyApiController@index");
    Route::get("/orders/{saleId}", "ProfitfyApiController@show");
});
//rotas consumida por terceiros: unicodrop
Route::group(["middleware" => ["auth:api", "scopes:sale", "throttle:120,1"]], function () {
    Route::get("/orders", "UnicoDropApiController@index");
    Route::get("/orders/{saleId}", "UnicoDropApiController@show");
});

//rotas consumida por terceiros: abstrato
Route::group(["middleware" => ["auth:api", "scopes:sale", "throttle:120,1"]], function () {
    Route::get("/orders", "ProfitfyApiController@index");
    Route::get("/orders/{saleId}", "ProfitfyApiController@show");
});
