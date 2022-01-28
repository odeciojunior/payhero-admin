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

          Route::post('/products/updateproducttype/{id}', 'ProductsApiController@updateProductType')->name('api.products.updateproducttype')
               ->middleware('permission:projects_manage|sales_manage');
          Route::post('/products/userproducts', 'ProductsApiController@getProducts')->name('api.products.getproducts')
               ->middleware('permission:projects_manage|sales_manage');
          Route::post('/products/getsignedurl', 'ProductsApiController@getSignedUrl')->name('api.products.getsignedurl')
               ->middleware('permission:projects_manage|sales_manage');
          Route::post('/products/verifyproductinplan', 'ProductsApiController@verifyProductInPlan')->name('api.products.verifyproductinplan')
               ->middleware('permission:projects_manage|sales_manage');

          //role:account_owner|admin|attendance|finantial
          Route::get('/products/saleproducts/{saleId}', 'ProductsApiController@getProductBySale')->name('api.products.saleproducts')
               ->middleware('permission:sales|contestations|trackings|finances|report_pending');
     }
);
