<?php

Route::group(
    [
        'middleware' => ['web', 'auth', 'role:account_owner|admin|attendance']
    ],
    function() {
        Route::get('/trackings/download/{filename}', 'TrackingsController@download');
        Route::resource('/trackings', 'TrackingsController')->only('index');
    }
);
