<?php

Route::group(['middleware' => 'web', 'prefix' => 'usuarios', 'namespace' => 'Modules\Usuario\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'UsuarioController@index',
        'as' => 'usuarios',
    ]);

    Route::get('/cadastro', [
        'uses' => 'UsuarioController@cadastro',
        'as' => 'usuarios.cadastro',
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
