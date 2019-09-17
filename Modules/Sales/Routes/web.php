<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'sales'], function() {
    Route::get('/', [
        'as'   => 'sales',
        'uses' => 'SalesController@index',
    ]);

    Route::get('/getsales', [
        'uses' => 'SalesController@getSales',
    ]);

    Route::post('/getcsvsales', [
        'as'   => 'sales.getcsvsales',
        'uses' => 'SalesController@getCsvSales',
    ]);

    Route::post('/venda/detalhe', [
        'as'   => 'sales.detail',
        'uses' => 'SalesController@getSaleDetail',
    ]);

    Route::post('/venda/estornar', [
        'as'   => 'sales.refund',
        'uses' => 'SalesController@estornarVenda',
    ]);

    Route::post('/update/trackingcode', [
        'as'   => 'sales.updatetrackingcode',
        'uses' => 'SalesController@updateTrackingCode',
    ]);

    Route::post('/update/trackingcode/{sale}', [
        'as'   => 'sales.sentemailtrackingcode',
        'uses' => 'SalesController@sendEmailUpdateTrackingCode',
    ]);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/vendas'], function() {
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

Route::group(['middleware' => 'api', 'prefix' => 'api/sales'], function() {
    Route::get('/get/{pass}', [
        'uses' => 'SalesApiController@index',
    ]);
});
