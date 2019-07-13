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

Route::middleware(['web', 'auth'])->prefix('reports')->group(function() {
    Route::get('/', 'ReportsController@index')->name('reports.index');

    Route::get('/getValues/{project_id}', 'ReportsController@getValues')->name('reports.values');
    Route::get('/getsalesbyorigin', 'ReportsController@getSalesByOrigin')->name('reports.salesbyorigin');
});




