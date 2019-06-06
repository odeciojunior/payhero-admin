<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'shipping', 'namespace' => 'Modules\Shipping\Http\Controllers'], function()
{
    Route::post('/store', [
        'uses' => 'ShippingController@store',
        'as' => 'shipping.store',
    ]);

    Route::post('/update', [
        'uses' => 'ShippingController@update',
        'as' => 'shipping.update',
    ]);

    Route::post('/delete', [
        'uses' => 'ShippingController@delete',
        'as' => 'shipping.delete',
    ]);

});
