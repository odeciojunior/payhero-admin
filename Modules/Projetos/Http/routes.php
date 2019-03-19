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

    Route::post('/deletarprojeto', [
        'uses' => 'ProjetosController@deletarProjeto',
        'as' => 'projetos.deletar',
    ]);

    Route::post('/cadastrarprojeto', [
        'uses' => 'ProjetosController@cadastrarProjeto',
        'as' => 'projetos.cadastrarprojeto',
    ]);

    Route::get('/projeto/{id}',[
        'as' => 'projetos.detalhes',
        'uses' => 'ProjetosController@projeto'
    ]);

    Route::get('/getconfiguracoesprojeto/{id}',[
        'as' => 'projetos.configuracoes',
        'uses' => 'ProjetosController@getConfiguracoesProjeto'
    ]);

    Route::get('/getdadosprojeto/{id}',[
        'as' => 'projetos.dados',
        'uses' => 'ProjetosController@getDadosProjeto'
    ]);

    Route::post('/addmaterialextra',[
        'as' => 'projetos.addmaterialextra',
        'uses' => 'ProjetosController@addMaterialExtra'
    ]);

    Route::post('/deletarmaterialextra',[
        'as' => 'projetos.deletarmaterialextra',
        'uses' => 'ProjetosController@deletarMaterialExtra'
    ]);

});

// 'middleware' => 'auth:api',
Route::group([ 'prefix' => 'api/projetos', 'namespace' => 'Modules\Projetos\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'ProjetosApiController@index',
    ]);

    Route::get('/{id}', [
        'uses' => 'ProjetosApiController@show',
    ]);

    Route::post('/', [
        'uses' => 'ProjetosApiController@store',
    ]);

    Route::put('/', [
        'uses' => 'ProjetosApiController@update',
    ]);

    Route::delete('/{id}', [
        'uses' => 'ProjetosApiController@delete',
    ]);

    
});
