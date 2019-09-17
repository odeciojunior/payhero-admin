<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('sales', 'SalesApiController')
    ->only('index')
    ->middleware(['web', 'auth']);

Route::group(
    [
        'middleware' => ['web', 'auth'],
        'prefix' => 'sales'
    ],
    function() {

        Route::get('/getsales', [
            'uses' => 'SalesApiController@getSales',
        ]);

        Route::post('/getcsvsales', [
            'as'   => 'sales.getcsvsales',
            'uses' => 'SalesApiController@getCsvSales',
        ]);

        Route::post('/detail', [
            'as'   => 'sales.detail',
            'uses' => 'SalesApiController@getSaleDetail',
        ]);

        Route::post('/refund', [
            'as'   => 'sales.refund',
            'uses' => 'SalesApiController@refundSale',
        ]);

        Route::post('/update/trackingcode', [
            'as'   => 'sales.updatetrackingcode',
            'uses' => 'SalesApiController@updateTrackingCode',
        ]);

        Route::post('/update/trackingcode/{sale}', [
            'as'   => 'sales.sentemailtrackingcode',
            'uses' => 'SalesApiController@sendEmailUpdateTrackingCode',
        ]);
    }
);
