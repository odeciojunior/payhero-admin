<?php

Route::group(['middleware' => 'web', 'prefix' => 'postback', 'namespace' => 'Modules\PostBack\Http\Controllers'], function() {

    Route::post('/pagarme', 'PostBackPagarmeController@postBackListener');

    Route::post('/ebanx', 'PostBackEbanxController@postBackListener');

    Route::post('/shopify/{project_id}', 'PostBackShopifyController@postBackListener');
    Route::get('/shopify/{project_id}', 'PostBackShopifyController@postBackListener');
});
