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
    Route::Resource('apps/woocommerce', 'WooCommerceController')
         ->only('index')->names('woocommerce.web')->middleware('permission:apps'); //@TODO renomear a rota para um nome melhor

    Route::get('apps/woocommerce/login/callback/', [
        'uses' => 'WooCommerceApiController@callbackWooCommerceIntegration',
        'as'   => 'WooCommerce.login.callback',
    ]);

});

Route::get('apps/woocommerce/app/index', function(){
    return view('woocommerce.woocommerceapp');
});
