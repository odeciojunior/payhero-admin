<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'dominios', 'namespace' => 'Modules\Domains\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'DomainsController@index',
        'as' => 'domains',
    ]);

    Route::get('/cadastro', [
        'uses' => 'DomainsController@cadastro',
        'as' => 'domains.cadastro',
    ]);

    Route::get('/editar/{id}', [
        'uses' => 'DomainsController@editarDominio',
        'as' => 'domains.editar',
    ]);

    Route::post('/editardominio', [
        'uses' => 'DomainsController@updateDominio',
        'as' => 'domains.update',
    ]);

    Route::post('/deletardominio', [
        'uses' => 'DomainsController@deletarDominio',
        'as' => 'domains.deletar',
    ]);

    Route::post('/cadastrardominio', [
        'uses' => 'DomainsController@cadastrarDominio',
        'as' => 'domains.cadastrardominio',
    ]);

    Route::post('/data-source',[
        'as' => 'domains.index',
        'uses' => 'DomainsController@index'
    ]);

    Route::post('/getformadddominio', [
        'uses' => 'DomainsController@getFormAddDominio',
        'as' => 'domains.getformadddominio',
    ]);

    Route::post('/getformeditardominio', [
        'uses' => 'DomainsController@getFormEditarDominio',
        'as' => 'domains.getformeditardominio',
    ]);

    Route::post('/detalhesdominio', [
        'uses' => 'DomainsController@detalhesDominio',
        'as' => 'domains.detalhesdominio',
    ]);

    Route::post('/removerregistrodns', [
        'uses' => 'DomainsController@removerRegistroDns',
        'as' => 'domains.removerregistrodns',
    ]);

});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/projetos/{id_projeto}/dominios', 'namespace' => 'Modules\Domains\Http\Controllers'], function(){

    Route::get('/', [
        'uses' => 'DomainsApiController@index',
    ]);

    Route::post('/', [
        'uses' => 'DomainsApiController@store',
    ]);

    Route::put('/', [
        'uses' => 'DomainsApiController@update',
    ]);

    Route::delete('/{id_dominio}', [
        'uses' => 'DomainsApiController@delete',
    ]);

    Route::get('/{id_dominio}', [
        'uses' => 'DomainsApiController@show',
    ]);

    Route::get('/getbancos', [
        'uses' => 'DomainsApiController@getBancos',
    ]);

    Route::post('/{id_dominio}/dns', [
        'uses' => 'DomainsApiController@storeDns',
    ]);

    Route::delete('/{id_dominio}/dns/{id_dns}', [
        'uses' => 'DomainsApiController@destroyDns',
    ]);

});
