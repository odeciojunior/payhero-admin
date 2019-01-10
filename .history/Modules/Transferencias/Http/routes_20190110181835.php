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

Route::group(['middleware' => 'web', 'prefix' => 'extrato', 'namespace' => 'Modules\Transferencias\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'TransferenciasController@extrato',
        'as' => 'extrato'
    ]);

    Route::post('/detalhessaldofuturo', [
        'uses' => 'TransferenciasController@detalhesSaldoFuturo',
        'as' => 'transferencias.detalhessaldofuturo'
    ]);
    
    Route::post('/historico', [
        'uses' => 'TransferenciasController@historico',
        'as' => 'transferencias.historico'
    ]);

    Route::post('/getsaldos', [
        'uses' => 'TransferenciasController@getSaldosHistorico',
        'as' => 'transferencias.getsaldos'
    ]);

});
