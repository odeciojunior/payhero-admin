<?php

Route::group(['middleware' => 'web', 'prefix' => 'transportadoras', 'namespace' => 'Modules\Transportadoras\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'TransportadorasController@index',
        'as' => 'transportadoras',
    ]);

    Route::get('/cadastro', [
        'uses' => 'TransportadorasController@cadastro',
        'as' => 'transportadoras.cadastro',
    ]);

    Route::get('/editar/{id}', [
        'uses' => 'TransportadorasController@editarTransportadora',
        'as' => 'transportadoras.editar',
    ]);

    Route::post('/editartransportadora', [
        'uses' => 'TransportadorasController@updateTransportadora',
        'as' => 'transportadoras.update',
    ]);

    Route::get('/deletartransportadora/{id}', [
        'uses' => 'TransportadorasController@deletarTransportadora',
        'as' => 'transportadoras.deletar',
    ]);

    Route::post('/cadastrartransportadora', [
        'uses' => 'TransportadorasController@cadastrarTransportadora',
        'as' => 'transportadoras.cadastrartransportadora',
    ]);

    Route::post('/data-source',[
        'as' => 'transportadoras.dadostransportadoras',
        'uses' => 'TransportadorasController@dadosTransportadora'
    ]);

    Route::post('/detalhe',[
        'as' => 'transportadoras.detalhes',
        'uses' => 'TransportadorasController@getDetalhesTransportadora'
    ]);

});
