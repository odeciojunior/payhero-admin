<?php

Route::group(['middleware' => 'web', 'prefix' => 'categorias', 'namespace' => 'Modules\Categorias\Http\Controllers'], function() {

    Route::get('/', [
        'uses' => 'CategoriasController@index',
        'as' => 'categorias',
    ]);

    Route::get('/cadastro', [
        'uses' => 'CategoriasController@cadastro',
        'as' => 'categorias.cadastro',
    ]);

    Route::get('/editar/{id}', [
        'uses' => 'CategoriasController@editarCategoria',
        'as' => 'categorias.editar',
    ]);

    Route::post('/editarcategoria', [
        'uses' => 'CategoriasController@updateCategoria',
        'as' => 'categorias.update',
    ]);

    Route::get('/deletarcategoria/{id}', [
        'uses' => 'CategoriasController@deletarCategoria',
        'as' => 'categorias.deletar',
    ]);

    Route::post('/cadastrarcategoria', [
        'uses' => 'CategoriasController@cadastrarCategoria',
        'as' => 'categorias.cadastrarcategoria',
    ]);

    Route::post('/data-source',[
        'as' => 'categorias.dadoscategorias',
        'uses' => 'CategoriasController@dadosCategoria'
    ]);

    Route::post('/detalhe',[
        'as' => 'usuario.detalhes',
        'uses' => 'CategoriasController@getDetalhesCategoria'
    ]);

});
