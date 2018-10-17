<?php

Route::group(['middleware' => 'web', 'prefix' => 'produtos', 'namespace' => 'Modules\Produtos\Http\Controllers'], function() {

    Route::get('/', [
        'uses' => 'ProdutosController@index',
        'as' => 'produtos',
    ]);

    Route::get('/cadastro', [
        'uses' => 'ProdutosController@cadastro',
        'as' => 'produtos.cadastro',
    ]);

    Route::get('/editar/{id}', [
        'uses' => 'ProdutosController@editarProduto',
        'as' => 'produtos.editar',
    ]);

    Route::post('/editarproduto', [
        'uses' => 'ProdutosController@updateProduto',
        'as' => 'produtos.update',
    ]);

    Route::get('/deletarproduto/{id}', [
        'uses' => 'ProdutosController@deletarProduto',
        'as' => 'produtos.deletar',
    ]);

    Route::post('/cadastrarproduto', [
        'uses' => 'ProdutosController@cadastrarProduto',
        'as' => 'produtos.cadastrarproduto',
    ]);

    Route::post('/data-source',[
        'as' => 'produtos.dadosprodutos',
        'uses' => 'ProdutosController@dadosProduto'
    ]);

    Route::post('/detalhe',[
        'as' => 'usuario.detalhes',
        'uses' => 'ProdutosController@getDetalhesProduto'
    ]);

});
