<?php

Route::group(
    [
        'middleware' => ['web'],
        'prefix' => 'postback',
        'namespace' => 'Modules\PostBack\Http\Controllers'
    ],
    function () {
        Route::post('/pagarme', 'PostBackPagarmeController@postBackListener');

        Route::post('/ebanx', 'PostBackEbanxController@postBackListener');

        Route::post('/notazz', 'PostBackNotazzController@postBackListener');

        Route::post('/trackingmore', 'PostBackTrackingmoreController@postBackListener');

        Route::post('/shopify/{project_id}/tracking', 'PostBackShopifyController@postBackTracking');

        Route::post('/shopify/{project_id}', 'PostBackShopifyController@postBackListener');

        Route::any('/getnet', 'PostBackGetnetController@postBackGetnet');

        Route::any('/woocommerce/{project_id}/tracking', 'PostBackWooCommerceController@postBackTracking');

        Route::any('/woocommerce/{project_id}/product/update', 'PostBackWooCommerceController@postBackProductUpdate');

        Route::any('/woocommerce/{project_id}/product/create', 'PostBackWooCommerceController@postBackProductCreate');

    }
);

