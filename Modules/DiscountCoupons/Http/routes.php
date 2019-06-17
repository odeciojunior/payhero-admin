<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => '', 'namespace' => 'Modules\DiscountCoupons\Http\Controllers'], function() {
    Route::Resource('/couponsdiscounts', 'DiscountCouponsController')
         ->only('index', 'create', 'store', 'show', 'edit', 'update', 'destroy');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/projetos/{id_projeto}/cuponsdesconto', 'namespace' => 'Modules\DiscountCoupons\Http\Controllers'], function() {

    Route::get('/', [
        'uses' => 'DiscountCouponsApiController@index',
    ]);

    Route::post('/', [
        'uses' => 'DiscountCouponsApiController@store',
    ]);

    Route::put('/', [
        'uses' => 'DiscountCouponsApiController@update',
    ]);

    Route::delete('/{id_cupom}', [
        'uses' => 'DiscountCouponsApiController@destroy',
    ]);

    Route::get('/{id_cupom}', [
        'uses' => 'DiscountCouponsApiController@show',
    ]);
});
