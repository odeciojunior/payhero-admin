<?php

Route::group(['middleware' => 'web', 'prefix' => 'transferencias', 'namespace' => 'Modules\Transferencias\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'TransferenciasController@index',
        'as' => 'transferencias'
    ]);

    Route::post('/saque', [
        'uses' => 'TransferenciasController@saque',
        'as' => 'transferencias.saque'
    ]);

    Route::post('/detalhesantecipacao', [
        'uses' => 'TransferenciasController@detalhesAntecipacao',
        'as' => 'transferencias.detalhesantecipacao'
    ]);

    Route::post('/confirmarantecipacao', [
        'uses' => 'TransferenciasController@confirmarAntecipacao',
        'as' => 'transferencias.confirmarantecipacao'
    ]);

    Route::post('/historicotransferencias', [
        'uses' => 'TransferenciasController@getTransferencias',
        'as' => 'transferencias.historicotransferencias'
    ]);

    Route::post('/cancelartransferencia', [
        'uses' => 'TransferenciasController@cancelarTransferencia',
        'as' => 'transferencias.cancelartransferencia'
    ]);

    Route::post('/historicoantecipacoes', [
        'uses' => 'TransferenciasController@getAntecipacoes',
        'as' => 'transferencias.historicoantecipacoes'
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
        'uses' => 'TransferenciasController@getSaldos',
        'as' => 'transferencias.getsaldos'
    ]);

});
