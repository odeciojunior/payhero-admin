<?php

Route::group(['middleware' => ['web','auth'], 'prefix' => 'afiliados', 'namespace' => 'Modules\Affiliates\Http\Controllers'], function()
{
    Route::get('/afiliar/{id_projeto}', 'AffiliatesController@create');

    Route::get('/meusafiliados', [
        'uses' => 'AffiliatesController@myAffiliates',
        'as'   => 'afiliados.meusafiliados'
    ]);

    Route::get('/minhasafiliacoes', [
        'uses' => 'AffiliatesController@myAffiliations',
        'as'   => 'afiliados.minhasafiliacoes'
    ]);

    Route::get('/getafiliadosprojeto/{id_projeto}', [
        'uses' => 'AffiliatesController@projectAffiliations',
        'as'   => 'afiliados.projectaffiliations'
    ]);

    Route::get('/minhasafiliacoes/{id_afiliacao}', [
        'uses' => 'AffiliatesController@details',
        'as'   => 'afiliados.afiliacao'
    ]);

    Route::post('/excluirafiliacao', [
        'uses' => 'AffiliatesController@delete',
        'as'   => 'afiliados.excluirafiliacao'
    ]);

    Route::post('/cancelarsolicitacao', [
        'uses' => 'AffiliatesController@cancelRequest',
        'as'   => 'afiliados.cancelrequest'
    ]);

    Route::post('/negarsolicitacao', [
        'uses' => 'AffiliatesController@denyRequest',
        'as'   => 'afiliados.denyrequest'
    ]);

    Route::post('/meusafiliados/data-source','AffiliatesController@myAffiliatesData');

    Route::post('/minhassolicitacoesafiliados/data-source','AffiliatesController@pendingAffiliations');

    Route::post('/minhasafiliacoespendentes/confirmar','AffiliatesController@confirmAffiliation');

    Route::post('/minhasafiliacoespendentes/data-source','AffiliatesController@myPendingAffiliations');
    
    Route::post('/setempresa','AffiliatesController@setAffiliationCompany');
    
    Route::post('/campanhas/data-source','CampaignsController@index');

    Route::post('/campanhas/cadastrar','CampaignsController@store');

    Route::post('/campanhas/getdadoscampanha','CampaignsController@details');

    Route::post('/campanhas/vendas','CampaignsController@sales');
    
});

/*
Route::group(['middleware' => 'auth:api', 'prefix' => 'api/afiliados', 'namespace' => 'Modules\Affiliates\Http\Controllers'], function()
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

    Route::delete('/minhasafiliacoes/solicitacoes/{id_solicitacao}', [
        'uses' => 'AfiliadosApiController@destroySolicitacao',
    ]);

    Route::post('/{id_projeto}', [
        'uses' => 'AfiliadosApiController@store',
    ]);

    Route::delete('/{id_projeto}/{id_afiliado}', [
        'uses' => 'AfiliadosApiController@destroy',
    ]);
    
});

*/
