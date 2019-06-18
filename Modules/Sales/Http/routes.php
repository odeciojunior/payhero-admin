<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'sales', 'namespace' => 'Modules\Sales\Http\Controllers'], function()
{
    Route::get('/',[
        'as' => 'sales',
        'uses' => 'SalesController@index'
    ]);

    Route::get('/getsales', [
        'uses' => 'SalesController@getSales',
    ]);

    Route::post('/venda/detalhe',[
        'as' => 'sales.detail',
        'uses' => 'SalesController@getSaleDetail'
    ]);

    Route::post('/venda/estornar',[
        'as' => 'sales.refund',
        'uses' => 'SalesController@estornarVenda'
    ]);

});


Route::group(['middleware' => 'auth:api', 'prefix' => 'api/vendas', 'namespace' => 'Modules\Sales\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'SalesController@getVendas',
    ]);

    Route::get('/{id_venda}', [
        'uses' => 'SalesController@detalhesVenda',
    ]);

    Route::post('/estornarvenda', [
        'uses' => 'SalesController@estornarVenda',
    ]);    

});
