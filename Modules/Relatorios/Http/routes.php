<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'relatorios', 'namespace' => 'Modules\Relatorios\Http\Controllers'], function()
{
    Route::resource('vendas','RelatoriosController');

    Route::post('/vendas/data-source',[
        'as' => 'relatorios.dadosvendas',
        'uses' => 'RelatoriosController@dadosVendas'
    ]);

    Route::post('/venda/detalhe',[
        'as' => 'relatorios.vendadetalhe',
        'uses' => 'RelatoriosController@getDetalhesVenda'
    ]);

});


