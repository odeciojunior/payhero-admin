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

// Route::prefix('digitalmanager')->group(function() {
//     Route::get('/', 'DigitalManagerController@index');
// });

Route::group(['middleware' => ['web', 'auth']], function() {

    Route::Resource('apps/digitalmanager', 'DigitalManagerController')
         ->only('index', 'create', 'store', 'edit', 'update', 'show', 'destroy');
});
