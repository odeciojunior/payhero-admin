<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'apps/shopify', 'namespace' => 'Modules\Shopify\Http\Controllers'], function()
{
    Route::Resource('/', 'ShopifyController')->only('index', 'store');
    
/*    Route::get('/', [
        'uses' => 'ShopifyController@index',
        'as' => 'shopify.index'
    ]);

    Route::post('/adicionarintegracao', [
        'uses' => 'ShopifyController@store',
        'as' => 'shopify.adicionarintegracao'
    ]);*/

});

Route::group(['prefix' => 'apps/shopify', 'namespace' => 'Modules\Shopify\Http\Controllers'], function()
{

    Route::post('/webhook/{id_projeto}', [
        'uses' => 'ShopifyController@webHook',
        'as' => 'shopify.webhook'
    ]);

    Route::get('/webhook/{id_projeto}', [
        'uses' => 'ShopifyController@webHook',
        'as' => 'shopify.webhook'
    ]);

});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/apps/shopify', 'namespace' => 'Modules\Shopify\Http\Controllers'], function()
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

