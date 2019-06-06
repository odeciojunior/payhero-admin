<?php

Route::group(
    [
        'prefix'     => 'transfers',
        'middleware' => ['web'],
        'namespace' => 'Modules\Finances\Http\Controllers'
    ],
    function() {
        // rotas publicas

    }
);

Route::group(
    [
        'prefix'     => 'transfers',
        'middleware' => ['web','auth'],
        'namespace' => 'Modules\Finances\Http\Controllers'
    ],
    function() {
        // rotas autenticadas

        Route::get('/', 'WithdrawalController@index')->name('transfers');


    }
);

Route::group(['middleware' => 'web', 'prefix' => 'transferencias', 'namespace' => 'Modules\Finances\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'FinancesController@index',
        'as' => 'transferencias'
    ]);

    Route::post('/saque', [
        'uses' => 'FinancesController@saque',
        'as' => 'finances.saque'
    ]);

    Route::post('/detalhesantecipacao', [
        'uses' => 'FinancesController@detalhesAntecipacao',
        'as' => 'finances.detalhesantecipacao'
    ]);

    Route::post('/confirmarantecipacao', [
        'uses' => 'FinancesController@confirmarAntecipacao',
        'as' => 'finances.confirmarantecipacao'
    ]);

    Route::post('/historicofinances', [
        'uses' => 'FinancesController@getfinances',
        'as' => 'finances.historicofinances'
    ]);

    Route::post('/cancelartransferencia', [
        'uses' => 'FinancesController@cancelarTransferencia',
        'as' => 'finances.cancelartransferencia'
    ]);

    Route::post('/cancelarantecipacao', [
        'uses' => 'FinancesController@cancelarAntecipacao',
        'as' => 'finances.cancelarantecipacao'
    ]);

    Route::post('/historicoantecipacoes', [
        'uses' => 'FinancesController@getAntecipacoes',
        'as' => 'finances.historicoantecipacoes'
    ]);

});

Route::group(['middleware' => 'web', 'prefix' => 'extrato', 'namespace' => 'Modules\Finances\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'FinancesController@extrato',
        'as' => 'extrato'
    ]);

    Route::post('/detalhessaldofuturo', [
        'uses' => 'FinancesController@detalhesSaldoFuturo',
        'as' => 'finances.detalhessaldofuturo'
    ]);
    
    Route::post('/historico', [
        'uses' => 'FinancesController@historico',
        'as' => 'finances.historico'
    ]);

    Route::post('/getsaldos', [
        'uses' => 'FinancesController@getSaldos',
        'as' => 'finances.getsaldos'
    ]);

});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/financas', 'namespace' => 'Modules\Finances\Http\Controllers'], function()
{
    Route::get('/getsaldos', [
        'uses' => 'FinancesController@getSaldosDashboard',
    ]);

});
