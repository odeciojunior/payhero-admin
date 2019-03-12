<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/layout', 'namespace' => 'Modules\Core\Http\Controllers'], function()
{
    Route::get('/getmenulateral', [
        'uses' => 'LayoutController@getMenuLateral',
    ]);

});
