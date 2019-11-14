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

Route::group(['middleware' => ['web', 'auth']], function() {
    Route::Resource('apps/shopify', 'ShopifyController')
         ->only('index');

    Route::get('apps/shopify/login/callback/', [
        'uses' => 'ShopifyApiController@callbackShopifyIntegration',
        'as'   => 'shopify.login.callback',
    ]);

});

Route::get('apps/shopify/app/index', function(){
    return view('shopify.shopifyapp');
});