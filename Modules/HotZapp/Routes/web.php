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

//Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Modules\HotZapp\Http\Controllers'], function() {
//
//    Route::resource('/hotzapp', 'HotZappController')->only('index', 'create', 'store', 'edit', 'update','destroy')
//         ->names('hotzapp');
//});
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::Resource('apps/hotzapp', 'HotZappController')->only('index', 'create', 'store', 'edit', 'update', 'show');
});
//Route::prefix('hotzapp')->group(function() {
//    Route::get('/', 'HotZappController@index');
//});
