<?php


use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::resource('apps/melhorenvio', 'MelhorenvioController')
        ->only('index')
        ->names('melhorenvio');

    Route::view('/apps/melhorenvio/tutorial', 'melhorenvio::tutorial')->name('melhorenvio.tutorial');
});
