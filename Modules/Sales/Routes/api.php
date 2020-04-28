<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'role:account_owner|admin|attendance', 'scopes:admin', 'setUserAsLogged'],
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
        Route::post('/saleresendemail', 'SalesApiController@saleReSendEmail');
    }
);

Route::apiResource('sales', 'SalesApiController')
     ->only('index', 'show')
     ->middleware(['auth:api', 'scopes:admin', 'setUserAsLogged']);

Route::group(['middleware' => ['auth:api', 'scopes:sale', 'throttle:30,1'], 'prefix' => 'profitfy',], function () {
    Route::get('/{checkoutId}', 'SalesApiController@showExternal');
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
