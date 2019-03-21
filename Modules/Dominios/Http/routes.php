<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'dominios', 'namespace' => 'Modules\Dominios\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'DominiosController@index',
        'as' => 'dominios',
    ]);

    Route::get('/cadastro', [
        'uses' => 'DominiosController@cadastro',
        'as' => 'dominios.cadastro',
    ]);

    Route::get('/editar/{id}', [
        'uses' => 'DominiosController@editarDominio',
        'as' => 'dominios.editar',
    ]);

    Route::post('/editardominio', [
        'uses' => 'DominiosController@updateDominio',
        'as' => 'dominios.update',
    ]);

    Route::post('/deletardominio', [
        'uses' => 'DominiosController@deletarDominio',
        'as' => 'dominios.deletar',
    ]);

    Route::post('/cadastrardominio', [
        'uses' => 'DominiosController@cadastrarDominio',
        'as' => 'dominios.cadastrardominio',
    ]);

    Route::post('/data-source',[
        'as' => 'dominios.dadosdominios',
        'uses' => 'DominiosController@dadosDominios'
    ]);

    Route::post('/getformadddominio', [
        'uses' => 'DominiosController@getFormAddDominio',
        'as' => 'dominios.getformadddominio',
    ]);

    Route::post('/getformeditardominio', [
        'uses' => 'DominiosController@getFormEditarDominio',
        'as' => 'dominios.getformeditardominio',
    ]);

    Route::post('/detalhesdominio', [
        'uses' => 'DominiosController@detalhesDominio',
        'as' => 'dominios.detalhesdominio',
    ]);

    Route::post('/removerregistrodns', [
        'uses' => 'DominiosController@removerRegistroDns',
        'as' => 'dominios.removerregistrodns',
    ]);

});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/projetos/{id_projeto}/dominios', 'namespace' => 'Modules\Dominios\Http\Controllers'], function(){

    Route::get('/', [
        'uses' => 'DominiosApiController@index',
    ]);

    Route::post('/', [
        'uses' => 'DominiosApiController@store',
    ]);

    Route::put('/', [
        'uses' => 'DominiosApiController@update',
    ]);

    Route::delete('/{id_dominio}', [
        'uses' => 'DominiosApiController@delete',
    ]);

    Route::get('/{id_dominio}', [
        'uses' => 'DominiosApiController@show',
    ]);

    Route::get('/getbancos', [
        'uses' => 'DominiosApiController@getBancos',
    ]);

});
