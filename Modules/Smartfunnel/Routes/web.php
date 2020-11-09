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

// Route::prefix('smartfunnel')->group(function() {
//     Route::get('/', 'SmartfunnelController@index');
// });


Route::group(['middleware' => ['web', 'auth', 'setUserAsLogged']], function() {

    Route::Resource('apps/smartfunnel', 'SmartfunnelController')
         ->only('index', 'create', 'store', 'edit', 'update', 'show', 'destroy');
});