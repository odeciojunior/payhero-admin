<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'mobile'], function() {
    Route::get('/user', 'MobileController@user')->name('mobile.user');
    Route::get('/companies', 'MobileController@companies')->name('mobile.companies');
    Route::get('/balances', 'MobileController@balances')->name('mobile.balances');
    Route::get('/sales', 'MobileController@sales')->name('mobile.sales');
    Route::get('/withdrawals', 'MobileController@withdrawals')->name('mobile.withdrawals');
    Route::post('/withdrawals', 'MobileController@withdrawalsStore')->name('mobile.withdrawals.store');
});
