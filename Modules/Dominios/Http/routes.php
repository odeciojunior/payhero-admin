<?php

Route::group(['middleware' => 'web', 'prefix' => 'dominios', 'namespace' => 'Modules\Dominios\Http\Controllers'], function()
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

});
