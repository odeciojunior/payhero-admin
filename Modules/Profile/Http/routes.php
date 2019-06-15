<?php

Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Modules\Profile\Http\Controllers'], function() {

    Route::resource('/profile', 'ProfileController')->only('index', 'create', 'store', 'edit', 'update', 'destroy')->names('profile');
/*
    Route::get('/', [
        'uses' => 'ProfileController@index',
        'as'   => 'profile'
    ]);

    Route::post('/update', [
        'uses' => 'ProfileController@update',
        'as'   => 'profile.update'
    ]);
*/
    Route::post('/changepassword', [
        'uses' => 'ProfileController@changePassword',
        'as'   => 'profile.changepassword'
    ]);

});
