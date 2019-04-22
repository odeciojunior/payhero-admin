<?php

Route::group(['middleware' => 'web', 'prefix' => 'postback', 'namespace' => 'Modules\PostBack\Http\Controllers'], function() {

    Route::post('/', 'PostBackController@postBackListener');

});
