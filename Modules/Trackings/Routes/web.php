<?php

Route::group(
    [
        'middleware' => ['web', 'auth']
    ],
    function() {
        Route::resource('/trackings', 'TrackingsController')->only('index');
    }
);
