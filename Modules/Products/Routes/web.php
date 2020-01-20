<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth', 'role:account_owner|admin', 'setUserAsLogged'],
    ],
    function() {
        Route::resource('/products', 'ProductsController')->only('index', 'edit', 'create');
    }
);

