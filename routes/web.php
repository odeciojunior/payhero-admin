<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/terms', function () {
    return view('terms.terms');
});

Route::group(
    [
        'prefix'     => 'dev',
        'as'         => 'dev.',
        'middleware' => ['web', 'auth'],
        'namespace'  => '\App\Http\Controllers\Dev',
    ],
    function() {
        // rotas autenticadas
        Route::resource('/teste', 'TesteController')->names('teste');
    }
);


Auth::routes();

