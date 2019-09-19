<?php


Route::group(
    [
        'middleware' => ['web', 'auth'],
        'as' => 'hotzapp.'
    ],
    function() {
        Route::resource('/apps/hotzapp', 'HotZappController')->only('index');
    }
);
