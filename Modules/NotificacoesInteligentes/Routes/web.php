<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(
    [
        'middleware' => ['web', 'auth','permission:apps'],
        'as' => 'notificacoesinteligentes'
    ],
    function() {
        Route::resource('/apps/notificacoesinteligentes', 'NotificacoesInteligentesController')->only('index');
    }
);