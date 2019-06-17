<?php

Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Modules\Profile\Http\Controllers'], function() {

    Route::resource('/profile', 'ProfileController')->only('index', 'create', 'store', 'edit', 'update', 'destroy')
         ->names('profile');

    Route::post('/profile/changepassword', 'ProfileController@changePassword')->name('profile.changepassword');
});
