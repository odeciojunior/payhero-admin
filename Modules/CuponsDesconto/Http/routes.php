<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'cuponsdesconto', 'namespace' => 'Modules\CuponsDesconto\Http\Controllers'], function()
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

    Route::post('/editarcupom', [
        'uses' => 'CuponsDescontoController@updateCupomDesconto',
        'as' => 'cuponsdesconto.update',
    ]);

    Route::post('/deletarcupom', [
        'uses' => 'CuponsDescontoController@deletarCupomDesconto',
        'as' => 'cuponsdesconto.deletar',
    ]);

    Route::post('/cadastrarcupom', [
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

    Route::post('/getformaddcupom',[
        'as' => 'cuponsdesconto.getformaddcupom',
        'uses' => 'CuponsDescontoController@getFormAddCupom'
    ]);

    Route::post('/getformeditarcupom',[
        'as' => 'cuponsdesconto.getformeditarcupom',
        'uses' => 'CuponsDescontoController@getFormEditarCupom'
    ]);

});
