<?php

Route::group(['middleware' => 'web', 'prefix' => 'transferencias', 'namespace' => 'Modules\Transferencias\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'TransferenciasController@index',
        'as' => 'transferencias'
    ]);

    Route::post('/detalhesantecipacao', [
        'uses' => 'TransferenciasController@detalhesAntecipacao',
        'as' => 'transferencias.detalhesantecipacao'
    ]);
    
});
