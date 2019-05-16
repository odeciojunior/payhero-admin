<?php

Route::group(['middleware' => 'web', 'prefix' => 'api', 'namespace' => 'Modules\Authentication\Http\Controllers'], function()
{
    Route::post('/login', 'AuthenticationApiController@login');

    Route::post('/logout', 'AuthenticationApiController@logout');
});
