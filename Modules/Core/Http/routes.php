<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'layout', 'namespace' => 'Modules\Core\Http\Controllers'], function()
{
    Route::get('/getmenulateral', [
        'uses' => 'LayoutController@getMenuLateral',
    ]);

});
