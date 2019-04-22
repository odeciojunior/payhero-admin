<?php

Route::group(['middleware' => 'web', 'prefix' => 'checkout', 'namespace' => 'Modules\Checkout\Http\Controllers'], function()
{
    Route::post('/', 'CheckoutController@checkout');

    Route::post('/getparcelas', 'CheckoutController@getParcelas');

    Route::post('/pagamentocartao', 'CheckoutController@pagamentoCartao');

    Route::post('/pagamentoboleto', 'CheckoutController@pagamentoBoleto');

});
