<?php

Route::group(['middleware' => ['web','auth'], 'prefix' => 'afiliados', 'namespace' => 'Modules\Afiliados\Http\Controllers'], function()
{
    Route::get('/afiliar/{id_projeto}', 'AfiliadosController@afiliar');

    Route::get('/meusafiliados', [
        'uses' => 'AfiliadosController@meusAfiliados',
        'as'   => 'afiliados.meusafiliados'
    ]);

    Route::get('/minhasafiliacoes', [
        'uses' => 'AfiliadosController@minhasAfiliacoes',
        'as'   => 'afiliados.minhasafiliacoes'
    ]);

    Route::get('/getafiliadosprojeto/{id_projeto}', [
        'uses' => 'AfiliadosController@getAfiliadosProjeto',
        'as'   => 'afiliados.getafiliadosprojeto'
    ]);

    Route::get('/minhasafiliacoes/{id_afiliacao}', [
        'uses' => 'AfiliadosController@afiliacao',
        'as'   => 'afiliados.afiliacao'
    ]);

    Route::get('/getdetalhesafiliacao/{id_projeto}', [
        'uses' => 'AfiliadosController@getDetalhesAfiliacao',
        'as'   => 'afiliados.getdetalhesafiliacao'
    ]);

    Route::post('/excluirafiliacao', [
        'uses' => 'AfiliadosController@excluirAfiliacao',
        'as'   => 'afiliados.excluirafiliacao'
    ]);

    Route::post('/cancelarsolicitacao', [
        'uses' => 'AfiliadosController@cancelarSolicitacao',
        'as'   => 'afiliados.cancelarsolicitacao'
    ]);

    Route::post('/negarsolicitacao', [
        'uses' => 'AfiliadosController@negarSolicitacao',
        'as'   => 'afiliados.negarsolicitacao'
    ]);

    Route::post('/meusafiliados/data-source','AfiliadosController@dadosMeusAfiliados');

    Route::post('/minhassolicitacoesafiliados/data-source','AfiliadosController@dadosAfiliacoesPendentes');

    Route::post('/minhasafiliacoespendentes/data-source','AfiliadosController@dadosMinhasAfiliacoesPendentes');
    
    Route::post('/minhasafiliacoespendentes/confirmar','AfiliadosController@confirmarAfiliacao');
    
    Route::post('/setempresa','AfiliadosController@setEmpresaAfiliacao');
    
    Route::post('/campanhas/data-source','CampanhasController@getDadosCampanhas');

    Route::post('/campanhas/cadastrar','CampanhasController@cadastrar');

    Route::post('/campanhas/getdadoscampanha','CampanhasController@campanha');

    Route::post('/campanhas/vendas','CampanhasController@vendas');
    
});


Route::group(['middleware' => 'auth:api', 'prefix' => 'api/afiliados', 'namespace' => 'Modules\Afiliados\Http\Controllers'], function()
{
    Route::get('/meusafiliados', [
        'uses' => 'AfiliadosApiController@meusAfiliados',
    ]);

    Route::get('/meusafiliados/solicitacoes', [
        'uses' => 'AfiliadosApiController@meusAfiliadosSolicitacoes',
    ]);

    Route::get('/minhasafiliacoes', [
        'uses' => 'AfiliadosApiController@minhasAfiliacoes',
    ]);

    Route::get('/minhasafiliacoes/solicitacoes', [
        'uses' => 'AfiliadosApiController@minhasAfiliacoesSolicitacoes',
    ]);
    
});