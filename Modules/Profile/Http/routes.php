<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'perfil', 'namespace' => 'Modules\Profile\Http\Controllers'], function() {

    Route::get('/', [
        'uses' => 'ProfileController@index',
        'as'   => 'profile'
    ]);

    Route::post('/update', [
        'uses' => 'ProfileController@update',
        'as'   => 'profile.update'
    ]);

    Route::post('/alterarsenha', [
        'uses' => 'ProfileController@changePassword',
        'as'   => 'profile.changepassword'
    ]);

});
