<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin','permission:apps|projects'],
    ],
    function() {
        Route::get('/apps/woocommerce', 'WooCommerceApiController@index');
        Route::post('/apps/woocommerce', 'WooCommerceApiController@store')->middleware('permission:apps_manage');

        Route::get('/apps/woocommerce/user-companies', [
            'uses' => 'WooCommerceApiController@getCompanies',
            'as'   => 'woocommerce.getcompanies',
        ]);
        Route::post('/apps/woocommerce/undointegration', [
            'uses' => 'WooCommerceApiController@undoIntegration',
            'as'   => 'woocommerce.undointegration',
        ]);

        Route::post('/apps/woocommerce/reintegration', [
            'uses' => 'WooCommerceApiController@reIntegration',
            'as'   => 'woocommerce.reintegration',
        ]);

        Route::post('/apps/woocommerce/synchronize/products', [
            'uses' => 'WooCommerceApiController@synchronizeProducts',
            'as'   => 'woocommerce.synchronize.product',
        ]);

        Route::post('/apps/woocommerce/synchronize/trackings', [
            'uses' => 'WooCommerceApiController@synchronizeTrackings',
            'as'   => 'woocommerce.synchronize.trackings',
        ])->middleware('throttle:60,1');

        Route::post('/apps/woocommerce/keys/update', [
            'uses' => 'WooCommerceApiController@keysUpdate',
            'as'   => 'woocommerce.keys.update',
        ]);

        Route::post('/apps/woocommerce/keys/get', [
            'uses' => 'WooCommerceApiController@keysGet',
            'as'   => 'woocommerce.keys.get',
        ]);

        Route::post('/apps/woocommerce/updatetoken', [
            'uses' => 'WooCommerceApiController@updateToken',
            'as'   => 'woocommerce.updatetoken',
        ]);

        Route::post('/apps/woocommerce/verifypermissions', [
            'uses' => 'WooCommerceApiController@verifyPermission',
            'as'   => 'woocommerce.verifypermissions',
        ]);

        Route::post('/apps/woocommerce/skiptocart', [
            'uses' => 'WooCommerceApiController@setSkipToCart',
            'as'   => 'woocommerce.skiptocart',
        ]);
    }
);
