<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'apps/shopify', 'namespace' => 'Modules\Shopify\Http\Controllers'], function() {
    Route::Resource('/', 'ShopifyController')->only('index', 'store');

    Route::post('/undointegration', [
        'uses' => 'ShopifyController@undoIntegration',
        'as'   => 'shopify.undointegration',
    ]);

    Route::post('/reintegration', [
        'uses' => 'ShopifyController@reIntegration',
        'as'   => 'shopify.reintegration',
    ]);

    Route::post('/synchronize/products', [
        'uses' => 'ShopifyController@synchronizeProducts',
        'as'   => 'shopify.synchronize.product',
    ]);

    Route::post('/synchronize/templates', [
        'uses' => 'ShopifyController@synchronizeTemplates',
        'as'   => 'shopify.synchronize.template',
    ]);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/apps/shopify', 'namespace' => 'Modules\Shopify\Http\Controllers'], function() {

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

