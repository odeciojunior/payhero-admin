<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'projetos', 'namespace' => 'Modules\Projects\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'ProjectsController@index',
        'as' => 'projetos',
    ]);

    Route::get('/cadastro', [
        'uses' => 'ProjectsController@create',
        'as' => 'projetos.create',
    ]);

    Route::post('/editarprojeto', [
        'uses' => 'ProjectsController@update',
        'as' => 'projetos.update',
    ]);

    Route::post('/deletarprojeto', [
        'uses' => 'ProjectsController@delete',
        'as' => 'projetos.deletar',
    ]);

    Route::post('/cadastrarprojeto', [
        'uses' => 'ProjectsController@store',
        'as' => 'projetos.store',
    ]);

    Route::get('/projeto/{id}',[
        'as' => 'projetos.detalhes',
        'uses' => 'ProjectsController@project'
    ]);

    Route::get('/getconfiguracoesprojeto/{id}',[
        'as' => 'projetos.configuracoes',
        'uses' => 'ProjectsController@edit'
    ]);

    Route::get('/getdadosprojeto/{id}',[
        'as' => 'projetos.dados',
        'uses' => 'ProjectsController@getDadosProjeto'
    ]);

    Route::post('/addmaterialextra',[
        'as' => 'projetos.addmaterialextra',
        'uses' => 'ProjectsController@addMaterialExtra'
    ]);

    Route::post('/deletarmaterialextra',[
        'as' => 'projetos.deletarmaterialextra',
        'uses' => 'ProjectsController@deletarMaterialExtra'
    ]);

});
/*
Route::group(['middleware' => 'auth:api', 'prefix' => 'api/projetos', 'namespace' => 'Modules\Projetos\Http\Controllers'], function()
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
*/
