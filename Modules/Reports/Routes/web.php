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

Route::middleware(['web', 'auth', 'setUserAsLogged'])->prefix('reports')->group(function() {
    Route::get('/sales', 'ReportsController@index')->name('reports.index')->middleware('role:account_owner|admin');

    Route::get('/checkouts', 'ReportsController@checkouts')->name('reports.checkouts')->middleware('role:account_owner|admin');

    Route::get('/getValues/{project_id}', 'ReportsController@getValues')->name('reports.values')->middleware('role:account_owner|admin');
    Route::get('/getsalesbyorigin', 'ReportsController@getSalesByOrigin')->name('reports.salesbyorigin')->middleware('role:account_owner|admin');

    Route::get('/projections', 'ReportsController@projections')->name('reports.projections')->middleware('role:account_owner|admin');

    Route::get('/coupons', 'ReportsController@coupons')->name('reports.coupons')->middleware('role:account_owner|admin');
});




