<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'relatorios', 'namespace' => 'Modules\Relatorios\Http\Controllers'], function()
{
    Route::get('vendas',[
        'as' => 'relatorios.vendas',
        'uses' => 'RelatoriosController@vendas'
    ]);

    Route::get('/getvendas', [
        'uses' => 'RelatoriosController@getVendas',
    ]);
    
    Route::post('/vendas/data-source',[
        'as' => 'relatorios.dadosvendas',
        'uses' => 'RelatoriosController@dadosVendas'
    ]);

    Route::post('/venda/detalhe',[
        'as' => 'relatorios.vendadetalhe',
        'uses' => 'RelatoriosController@getDetalhesVenda'
    ]);

    Route::post('/venda/estornar',[
        'as' => 'relatorios.estornarvenda',
        'uses' => 'RelatoriosController@estornarVenda'
    ]);

});


Route::group(['middleware' => 'auth:api', 'prefix' => 'api/vendas', 'namespace' => 'Modules\Relatorios\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'RelatoriosController@getVendas',
    ]);

    Route::get('/{id_venda}', [
        'uses' => 'RelatoriosController@detalhesVenda',
    ]);

    Route::post('/estornarvenda', [
        'uses' => 'RelatoriosController@estornarVenda',
    ]);    

});
