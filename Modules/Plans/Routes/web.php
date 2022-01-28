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

Route::group(['middleware' => ['web', 'auth']], function() {
    Route::Resource('/plans', 'PlansController')->only('index');

    Route::get('/plans/loading/stage1', function() {
        return view('plans::loading/create/stage1');
    });
    Route::get('/plans/loading/stage2', function() {
        return view('plans::loading/create/stage2');
    });
    Route::get('/plans/loading/stage3', function() {
        return view('plans::loading/create/stage3');
    });

    Route::get('/plans/create/stage1', function() {
        return view('plans::stage1-create');
    });
    Route::get('/plans/create/stage2', function() {
        return view('plans::stage2-create');
    });
    Route::get('/plans/create/stage3', function() {
        return view('plans::stage3-create');
    });
});
