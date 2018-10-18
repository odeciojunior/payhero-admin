<?php

Route::group(['middleware' => 'web', 'prefix' => 'planos', 'namespace' => 'Modules\Planos\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'PlanosController@index',
        'as' => 'planos',
    ]);

    Route::get('/cadastro', [
        'uses' => 'PlanosController@cadastro',
        'as' => 'planos.cadastro',
    ]);

    Route::get('/editar/{id}', [
        'uses' => 'PlanosController@editarPlano',
        'as' => 'planos.editar',
    ]);

    Route::post('/editarplano', [
        'uses' => 'PlanosController@updatePlano',
        'as' => 'planos.update',
    ]);

    Route::get('/deletarplano/{id}', [
        'uses' => 'PlanosController@deletarPlano',
        'as' => 'planos.deletar',
    ]);

    Route::post('/cadastrarplano', [
        'uses' => 'PlanosController@cadastrarPlano',
        'as' => 'planos.cadastrarplano',
    ]);

    Route::post('/data-source',[
        'as' => 'planos.dadosplanos',
        'uses' => 'PlanosController@dadosPlano'
    ]);

    Route::post('/detalhe',[
        'as' => 'usuario.detalhes',
        'uses' => 'PlanosController@getDetalhesPlano'
    ]);

});
