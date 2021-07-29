<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
    ],
    function() {
        Route::apiResource('products', 'ProductsApiController')
             ->only('index', 'show', 'edit', 'store', 'update', 'destroy', 'create')
             ->names('api.products')->middleware('role:account_owner|admin');
        Route::post('/products/userproducts', 'ProductsApiController@getProducts')->name('api.products.getproducts')
             ->middleware('role:account_owner|admin');
        Route::post('/products/getsignedurl', 'ProductsApiController@getSignedUrl')->name('api.products.getsignedurl')
             ->middleware('role:account_owner|admin|attendance|finantial');
        Route::post('/products/verifyproductinplan', 'ProductsApiController@verifyProductInPlan')->name('api.products.verifyproductinplan')
             ->middleware('role:account_owner|admin');
        Route::get('/products/saleproducts/{saleId}', 'ProductsApiController@getProductBySale')
             ->name('api.products.saleproducts')->middleware('role:account_owner|admin|attendance|finantial');
    }
);

