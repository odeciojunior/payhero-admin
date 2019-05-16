<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'recuperacaocarrinho', 'namespace' => 'Modules\CartRecovery\Http\Controllers'], function()
{
    Route::get('/',[
        'uses' => 'CartRecoveryController@index',
        'as' => 'cartrecovery' 
    ]);

    Route::post('/data-source',[
        'uses' => 'CartRecoveryController@cartRecoveryData'
    ]);

});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/carrinhosabandonados', 'namespace' => 'Modules\CartRecovery\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'CartRecoveryController@getCarrinhosAbandonados',
    ]);

});
