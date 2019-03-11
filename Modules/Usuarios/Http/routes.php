<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'usuarios', 'namespace' => 'Modules\Usuario\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'UsuarioController@index',
        'as' => 'usuarios',
    ]);

    Route::get('/cadastro', [
        'uses' => 'UsuarioController@cadastro',
        'as' => 'usuarios.cadastro',
    ]);

    Route::get('/editar/{id}', [
        'uses' => 'UsuarioController@editarUsuario',
        'as' => 'usuarios.editar',
    ]);

    Route::post('/editarusuario', [
        'uses' => 'UsuarioController@updateUsuario',
        'as' => 'usuarios.update',
    ]);

    Route::get('/deletarusuario/{id}', [
        'uses' => 'UsuarioController@deletarUsuario',
        'as' => 'usuarios.deletar',
    ]);

    Route::post('/cadastrarusuario', [
        'uses' => 'UsuarioController@cadastrarusuario',
        'as' => 'usuarios.cadastrarusuario',
    ]);

    Route::post('/data-source',[
        'as' => 'usuarios.dadosusuarios',
        'uses' => 'UsuarioController@dadosUsuarios'
    ]);

    Route::post('/detalhe',[
        'as' => 'usuario.detalhes',
        'uses' => 'UsuarioController@getDetalhesUsuario'
    ]);

});

Route::group(['middleware' => 'auth:api', 'prefix' => 'user', 'namespace' => 'Modules\Usuario\Http\Controllers'], function(){

    Route::get('/', [
        'uses' => 'UsuarioController@user',
    ]);
});