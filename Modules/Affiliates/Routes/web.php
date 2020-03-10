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

Route::group(['middleware' => ['web', 'auth', 'setUserAsLogged']], function() {
    Route::get('/affiliates/{projectId}', 'AffiliatesController@index')
         ->name('index');

    Route::get('/affiliates', 'AffiliatesController@projectAffiliates')
         ->name('projectaffiliates');
});

//Route::prefix('affiliates')->group(function() {
//    Route::get('/', 'AffiliatesController@index');
//});
