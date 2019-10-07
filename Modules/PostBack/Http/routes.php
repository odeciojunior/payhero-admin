<?php

Route::group(['middleware' => ['web','VerifyShopifyPostback'], 'prefix' => 'postback', 'namespace' => 'Modules\PostBack\Http\Controllers'], function() {

    Route::post('/pagarme', 'PostBackPagarmeController@postBackListener');

    Route::post('/ebanx', 'PostBackEbanxController@postBackListener');

    Route::post('/mercadopago', 'PostBackMercadoPagoController@postBackListener');

    Route::post('/shopify/{project_id}/tracking', 'PostBackShopifyController@postBackTracking');

    Route::post('/shopify/{project_id}', 'PostBackShopifyController@postBackListener');

    Route::post('/perfectlog', 'PostBackPerfectLogController@postBackListener');
});
