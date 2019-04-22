<?php

Route::group(['middleware' => 'web', 'prefix' => 'ferramentas/shopify', 'namespace' => 'Modules\Shopify\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'ShopifyController@index',
        'as' => 'ferramentas.shopify'
    ]);

    Route::post('/adicionarintegracao', [
        'uses' => 'ShopifyController@adicionarIntegracao',
        'as' => 'shopify.adicionarintegracao'
    ]);

    Route::post('/sincronizarintegracao', [
        'uses' => 'ShopifyController@sincronizarIntegracao',
        'as' => 'shopify.sincronizarintegracao'
    ]);


});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/aplicativos/shopify', 'namespace' => 'Modules\Shopify\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'ShopifyApiController@index',
    ]);

    Route::post('/', [
        'uses' => 'ShopifyApiController@store',
    ]);

    Route::post('/sincronizarintegracao', [
        'uses' => 'ShopifyApiController@sincronizarIntegracao',
    ]);
    
});

