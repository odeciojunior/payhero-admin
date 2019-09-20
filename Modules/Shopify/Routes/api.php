<?php


use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth'],
    ],
    function() {
        Route::apiResource('/apps/shopify/', 'ShopifyApiController')
            ->only('index', 'store');

        Route::post('/apps/shopify/undointegration', [
            'uses' => 'ShopifyController@undoIntegration',
            'as'   => 'shopify.undointegration',
        ]);

        Route::post('/apps/shopify/reintegration', [
            'uses' => 'ShopifyController@reIntegration',
            'as'   => 'shopify.reintegration',
        ]);

        Route::post('/apps/shopify/synchronize/products', [
            'uses' => 'ShopifyController@synchronizeProducts',
            'as'   => 'shopify.synchronize.product',
        ]);

        Route::post('/apps/shopify/synchronize/templates', [
            'uses' => 'ShopifyController@synchronizeTemplates',
            'as'   => 'shopify.synchronize.template',
        ]);

    }
);
