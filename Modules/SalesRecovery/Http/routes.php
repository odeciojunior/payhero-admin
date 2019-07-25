<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'recoverycart', 'namespace' => 'Modules\SalesRecovery\Http\Controllers'], function() {
    Route::get('/', [
        'uses' => 'SalesRecoveryController@index',
        'as'   => 'cartrecovery',
    ]);

    Route::get('/getabandonatedcarts', [
        'uses' => 'SalesRecoveryController@getAbandonatedCarts',
    ]);

    Route::post('/details', [
        'uses' => 'SalesRecoveryController@getAbandonatedCardsDetails',
    ]);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/carrinhosabandonados', 'namespace' => 'Modules\SalesRecovery\Http\Controllers'], function() {
    Route::get('/', [
        'uses' => 'SalesRecoveryController@getCarrinhosAbandonados',
    ]);
});

