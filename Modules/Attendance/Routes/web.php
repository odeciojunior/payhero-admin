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
        'middleware' => ['web', 'auth', 'setUserAsLogged'],
    ],
    function() {

        Route::Resource('/attendance', 'AttendanceController')
             ->only('index', 'create', 'show')->middleware('role:account_owner|admin|attendance')->names('attendance');
    }
);

//Route::prefix('attendance')->group(function() {
//    Route::get('/', 'AttendanceController@index');
//});
