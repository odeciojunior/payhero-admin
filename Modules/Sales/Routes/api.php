<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'role:account_owner|admin|attendance', 'scopes:admin'],
        'prefix'     => 'sales',
    ],
    function() {

        Route::get('/filters', [
            'uses' => 'SalesApiController@filters',
        ]);

        Route::post('/export', [
            'as'   => 'sales.export',
            'uses' => 'SalesApiController@export',
        ]);

        Route::get('/resume', [
            'as'   => 'sales.resume',
            'uses' => 'SalesApiController@resume',
        ]);
        Route::post('/refund/{transaction_id}', 'SalesApiController@refund');
        Route::post('/refund/billet/{transaction_id}', 'SalesApiController@refundBillet');
        Route::post('/newordershopify/{transaction_id}', 'SalesApiController@newOrderShopify');
        Route::post('/updaterefundobservation/{transaction_id}', 'SalesApiController@updateRefundObservation');
        Route::post('/saleresendemail', 'SalesApiController@saleReSendEmail');
        Route::get('/user-plans', 'SalesApiController@getPlans');
        Route::post('/set-observation/{transaction_id}', 'SalesApiController@setValueObservation');
    }
);

Route::apiResource('sales', 'SalesApiController')
     ->only('index', 'show')
     ->middleware(['auth:api', 'scopes:admin', 'setUserAsLogged']);


Route::group(['middleware' => ['auth:api', 'scopes:sale', 'throttle:120,1'], 'prefix' => 'profitfy',], function () {
    Route::get('/orders', 'SalesApiController@indexExternal');
    Route::get('/orders/{saleId}', 'SalesApiController@showExternal');
});

Route::group(
    [
        'middleware' => ['InternalApiAuth'],
        'prefix'     => 'sales',
    ],
    function() {

        Route::post('/saleprocess', [
            'uses' => 'SalesApiController@saleProcess',
        ]);
    }
);
