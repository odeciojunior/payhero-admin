<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'setUserAsLogged'],
    ],
    function() {
        Route::apiResource('/apps/shopify/', 'ShopifyApiController')
             ->only('index', 'store');
        Route::get('/apps/shopify/user-companies', [
            'uses' => 'ShopifyApiController@getCompanies',
            'as'   => 'shopify.getcompanies',
        ]);
        Route::post('/apps/shopify/undointegration', [
            'uses' => 'ShopifyApiController@undoIntegration',
            'as'   => 'shopify.undointegration',
        ]);

        Route::post('/apps/shopify/reintegration', [
            'uses' => 'ShopifyApiController@reIntegration',
            'as'   => 'shopify.reintegration',
        ]);

        Route::post('/apps/shopify/synchronize/products', [
            'uses' => 'ShopifyApiController@synchronizeProducts',
            'as'   => 'shopify.synchronize.product',
        ]);

        Route::post('/apps/shopify/synchronize/templates', [
            'uses' => 'ShopifyApiController@synchronizeTemplates',
            'as'   => 'shopify.synchronize.template',
        ]);

        Route::post('/apps/shopify/updatetoken', [
            'uses' => 'ShopifyApiController@updateToken',
            'as'   => 'shopify.updatetoken',
        ]);

        Route::post('/apps/shopify/verifypermissions', [
            'uses' => 'ShopifyApiController@verifyPermission',
            'as'   => 'shopify.verifypermissions',
        ]);
    }
);
