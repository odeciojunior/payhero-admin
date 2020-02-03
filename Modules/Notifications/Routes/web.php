<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth']
    ],
    function() {
        // No routes
    }
);
