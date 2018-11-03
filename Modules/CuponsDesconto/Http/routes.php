<?php

Route::group(['middleware' => 'web', 'prefix' => 'cuponsdesconto', 'namespace' => 'Modules\CuponsDesconto\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'CuponsDescontoController@index',
        'as' => 'cuponsdesconto',
    ]);

    Route::get('/cadastro', [
        'uses' => 'CuponsDescontoController@cadastro',
        'as' => 'cuponsdesconto.cadastro',
    ]);

    Route::get('/editar/{id}', [
        'uses' => 'CuponsDescontoController@editarCupomDesconto',
        'as' => 'cuponsdesconto.editar',
    ]);

    Route::post('/editarcupomdesconto', [
        'uses' => 'CuponsDescontoController@updateCupomDesconto',
        'as' => 'cuponsdesconto.update',
    ]);

    Route::get('/deletarcupomdesconto/{id}', [
        'uses' => 'CuponsDescontoController@deletarCupomDesconto',
        'as' => 'cuponsdesconto.deletar',
    ]);

    Route::post('/cadastrarcupomdesconto', [
        'uses' => 'CuponsDescontoController@cadastrarCupomDesconto',
        'as' => 'cuponsdesconto.cadastrarcupomdesconto',
    ]);

    Route::post('/data-source',[
        'as' => 'cuponsdesconto.dadoscupomdesconto',
        'uses' => 'CuponsDescontoController@dadosCuponsDesconto'
    ]);

    Route::post('/detalhe',[
        'as' => 'cuponsdesconto.detalhes',
        'uses' => 'CuponsDescontoController@getDetalhesCupomDesconto'
    ]);

});
