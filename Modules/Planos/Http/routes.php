<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'planos', 'namespace' => 'Modules\Planos\Http\Controllers'], function()
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

    Route::post('/deletarplano', [
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

    Route::post('/getformaddplano',[
        'as' => 'usuario.getformaddplano',
        'uses' => 'PlanosController@getFormAddPlano'
    ]);

    Route::post('/getformeditarplano',[
        'as' => 'usuario.getformeditarplano',
        'uses' => 'PlanosController@getFormEditarPlano'
    ]);

});


Route::group(['middleware' => 'auth:api', 'prefix' => 'api/projetos/{id_projeto}/planos', 'namespace' => 'Modules\Planos\Http\Controllers'], function(){

    Route::get('/', [
        'uses' => 'PlanosApiController@index',
    ]);

    Route::post('/', [
        'uses' => 'PlanosApiController@store',
    ]);

    Route::put('/', [
        'uses' => 'PlanosApiController@update',
    ]);

    Route::delete('/{id_plano}', [
        'uses' => 'PlanosApiController@destroy',
    ]);

    Route::get('/{id_plano}', [
        'uses' => 'PlanosApiController@show',
    ]);

});

Route::group([ 'prefix' => 'api/planos', 'namespace' => 'Modules\Planos\Http\Controllers'], function(){

    Route::get('/{cod_identificador}', [
        'uses' => 'PlanosApiController@planoCheckout',
    ]);

});
