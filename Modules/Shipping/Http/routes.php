<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => '', 'namespace' => 'Modules\Shipping\Http\Controllers'], function() {

    Route::Resource("/shippings", "ShippingController")
         ->only('index', 'create', 'store', 'show', 'edit', 'update', 'destroy');

    Route::post('/shipping/config/{project}', "ShippingController@updateConfig");
});
