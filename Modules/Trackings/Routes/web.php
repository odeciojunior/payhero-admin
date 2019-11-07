<?php

Route::group(
    [
        'middleware' => ['web', 'auth', 'role:account_owner|admin|attendance']
    ],
    function() {
        Route::resource('/trackings', 'TrackingsController')->only('index');
    }
);
