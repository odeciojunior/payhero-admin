<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'recoverycart', 'namespace' => 'Modules\CartRecovery\Http\Controllers'], function()
{
    Route::get('/',[
        'uses' => 'CartRecoveryController@index',
        'as' => 'cartrecovery' 
    ]);

    Route::get('/getabandonatedcarts',[
        'uses' => 'CartRecoveryController@getAbandonatedCarts'
    ]);

});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/carrinhosabandonados', 'namespace' => 'Modules\CartRecovery\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'CartRecoveryController@getCarrinhosAbandonados',
    ]);

});

