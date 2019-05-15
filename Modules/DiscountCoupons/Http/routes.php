<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'cuponsdesconto', 'namespace' => 'Modules\DiscountCoupons\Http\Controllers'], function()
{

    Route::post('/editarcupom', [
        'uses' => 'DiscountCouponsController@update',
        'as' => 'cuponsdesconto.update',
    ]);

    Route::post('/deletarcupom', [
        'uses' => 'DiscountCouponsController@delete',
        'as' => 'cuponsdesconto.delete',
    ]);

    Route::post('/cadastrarcupom', [
        'uses' => 'DiscountCouponsController@create',
        'as' => 'cuponsdesconto.create',
    ]);

    Route::post('/data-source',[
        'as' => 'cuponsdesconto.index',
        'uses' => 'DiscountCouponsController@index'
    ]);

    Route::post('/detalhe',[
        'as' => 'cuponsdesconto.details',
        'uses' => 'DiscountCouponsController@details'
    ]);

    Route::post('/getformaddcupom',[
        'as' => 'cuponsdesconto.create',
        'uses' => 'DiscountCouponsController@create'
    ]);

    Route::post('/getformeditarcupom',[
        'as' => 'cuponsdesconto.edit',
        'uses' => 'DiscountCouponsController@edit'
    ]);

});


Route::group(['middleware' => 'auth:api', 'prefix' => 'api/projetos/{id_projeto}/cuponsdesconto', 'namespace' => 'Modules\DiscountCoupons\Http\Controllers'], function(){

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
