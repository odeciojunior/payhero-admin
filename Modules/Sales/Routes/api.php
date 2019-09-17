<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth'],
        'prefix' => 'sales'
    ],
    function() {
        Route::apiResource('sales', 'DashboardApiController')
            ->only('index');

        Route::get('/', [
            'uses' => 'SalesApiController@getVendas',
        ]);

//        Route::get('/{id_venda}', [
//            'uses' => 'SalesApiController@detalhesVenda',
//        ]);

        Route::post('/estornarvenda', [
            'uses' => 'SalesApiController@estornarVenda',
        ]);

        Route::get('/getsales', [
            'uses' => 'SalesApiController@getSales',
        ]);

        Route::post('/getcsvsales', [
            'as'   => 'sales.getcsvsales',
            'uses' => 'SalesApiController@getCsvSales',
        ]);

        Route::post('/venda/detalhe', [
            'as'   => 'sales.detail',
            'uses' => 'SalesApiController@getSaleDetail',
        ]);

        Route::post('/venda/estornar', [
            'as'   => 'sales.refund',
            'uses' => 'SalesApiController@estornarVenda',
        ]);

        Route::post('/update/trackingcode', [
            'as'   => 'sales.updatetrackingcode',
            'uses' => 'SalesApiController@updateTrackingCode',
        ]);

        Route::post('/update/trackingcode/{sale}', [
            'as'   => 'sales.sentemailtrackingcode',
            'uses' => 'SalesApiController@sendEmailUpdateTrackingCode',
        ]);

        Route::get('/get/{pass}', [
            'uses' => 'SalesApiController@index',
        ]);
    }
);
