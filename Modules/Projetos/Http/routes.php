<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'projetos', 'namespace' => 'Modules\Projetos\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'ProjetosController@index',
        'as' => 'projetos',
    ]);

    Route::get('/cadastro', [
        'uses' => 'ProjetosController@cadastro',
        'as' => 'projetos.cadastro',
    ]);

    Route::get('/editar/{id}', [
        'uses' => 'ProjetosController@editarProjeto',
        'as' => 'projetos.editar',
    ]);

    Route::post('/editarprojeto', [
        'uses' => 'ProjetosController@updateProjeto',
        'as' => 'projetos.update',
    ]);

    Route::get('/deletarprojeto/{id}', [
        'uses' => 'ProjetosController@deletarProjeto',
        'as' => 'projetos.deletar',
    ]);

    Route::post('/cadastrarprojeto', [
        'uses' => 'ProjetosController@cadastrarProjeto',
        'as' => 'projetos.cadastrarprojeto',
    ]);

    Route::post('/data-source',[
        'as' => 'projetos.dadosprojetos',
        'uses' => 'ProjetosController@dadosProjeto'
    ]);

    Route::get('/projeto/{id}',[
        'as' => 'projetos.detalhes',
        'uses' => 'ProjetosController@projeto'
    ]);

});

