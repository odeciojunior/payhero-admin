<?php

Route::group(['middleware' => ['web', 'scopes:admin'], 'prefix' => 'postback', 'namespace' => 'Modules\PostBack\Http\Controllers'], function() {

    Route::post('/pagarme', 'PostBackPagarmeController@postBackListener');

    Route::post('/ebanx', 'PostBackEbanxController@postBackListener');

    Route::post('/mercadopago', 'PostBackMercadoPagoController@postBackListener');

    Route::post('/notazz', 'PostBackNotazzController@postBackListener');

    Route::post('/perfectlog', 'PostBackPerfectLogController@postBackListener');

    Route::post('/trackingmore', 'PostBackTrackingmoreController@postBackListener');
});

// ['web','VerifyShopifyPostback']

Route::group(['middleware' => ['web'], 'prefix' => 'postback', 'namespace' => 'Modules\PostBack\Http\Controllers'], function() {

    Route::post('/shopify/{project_id}/tracking', 'PostBackShopifyController@postBackTracking');

    Route::post('/shopify/{project_id}', 'PostBackShopifyController@postBackListener');
});

