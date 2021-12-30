<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
    ],
    function () {
        Route::get('products', 'ProductsApiController@index');
        Route::get('products/{id}', 'ProductsApiController@show');
        Route::get('products/{id}/edit', 'ProductsApiController@edit');

        Route::apiResource('products', 'ProductsApiController')
        ->only('store', 'update', 'destroy', 'create')
        ->names('api.products')->middleware('permission:products_manage');

        Route::put('/products/{id}/update-custom', 'ProductsApiController@updateCustom')
        ->name('api.products.updatecustom')
        ->middleware('permission:projects_manage|sales_manage');

        Route::post('/products/userproducts', 'ProductsApiController@getProducts')
        ->name('api.products.getproducts')
        ->middleware('permission:projects_manage|sales_manage');

        Route::post('/products/topselling', 'ProductsApiController@getTopSellingProducts')
        ->name('api.products.topselling')
        ->middleware('permission:projects_manage|sales_manage');

        Route::post('/products/getsignedurl', 'ProductsApiController@getSignedUrl')
        ->name('api.products.getsignedurl')
        ->middleware('permission:projects_manage|sales_manage');

        Route::post('/products/verifyproductinplan', 'ProductsApiController@verifyProductInPlan')
        ->name('api.products.verifyproductinplan')
        ->middleware('permission:projects_manage|sales_manage');

        Route::post('/products/verifyproductinplansale', 'ProductsApiController@verifyProductInPlanSale')
        ->name('api.products.verifyproductinplansale')
        ->middleware('permission:projects_manage|sales_manage');

        //role:account_owner|admin|attendance|finantial
        Route::post('/products/search', 'ProductsApiController@getProductFilter')
        ->name('api.products.filterproducts')
        ->middleware('role:account_owner|admin');

        Route::get('/product/{id}', 'ProductsApiController@getProductById')
        ->name('api.products.getproduct')
        ->middleware('role:account_owner|admin');

        Route::post('/products/products-variants', 'ProductsApiController@getProductsVariants')
        ->name('api.products.productsvariants')
        ->middleware('role:account_owner|admin');

        Route::get('/products/saleproducts/{saleId}', 'ProductsApiController@getProductBySale')
        ->name('api.products.saleproducts')
        ->middleware('permission:sales|contestations|trackings|finances|report_pending');
    }
);
