<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth', 'role:account_owner|admin|attendance'],
    ],
    function() {
        Route::get('/sales/download/{filename}', 'SalesController@download');
        Route::resource('/sales', 'SalesController')->only('index');
    }
);
