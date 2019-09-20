<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth'],
    ],
    function() {
        Route::apiResource('products', 'ProductsApiController')
             ->only('index', 'show', 'edit', 'store', 'update', 'destroy', 'create')
             ->names('api.products');
        Route::post('/products/userproducts', 'ProductsApiController@getProducts')->name('api.products.getproducts');
    }
);
