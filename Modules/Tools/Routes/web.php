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

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth', 'setUserAsLogged']], function() {
    Route::Resource('tools', 'ToolsController')->names('tools')->middleware('role:account_owner|admin');
});
//Route::prefix('tools')->group(function() {
//    Route::get('/', 'ToolsController@index');
//});
