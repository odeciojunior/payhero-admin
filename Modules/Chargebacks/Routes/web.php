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

Route::middleware(['web', 'auth', 'role:attendance|account_owner|admin'])->prefix('contestations')->group(function() {

    Route::get('/', 'ContestationsController@index')->name('contestations.index');

});

//Route::prefix('chargebacks')->group(function() {
//    Route::get('/', 'ChargebacksController@index');
//});
