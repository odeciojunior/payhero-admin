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
    ],
    function() {
        // rotas autenticadas
        // rotas para autenticação e registro de novos usuarios
        Route::get('/login', '\App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
        Route::post('/login', '\App\Http\Controllers\Auth\LoginController@login');
        Route::post('/logout', '\App\Http\Controllers\Auth\LoginController@logout')->name('logout');
        //somente para desenvolvimento, depois remover e deixar somente o metodo post para logout
        Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

        // Registration Routes...
//        Route::get('/register', '\App\Http\Controllers\Auth\NewRegisterController@index')->name('register');
//        Route::post('/register', '\App\Http\Controllers\Auth\NewRegisterController@store');

        // Password Reset Routes...
        Route::get('/password/reset', '\App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')
             ->name('password.request');
        Route::post('/password/email', '\App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')
             ->name('password.email');
        Route::get('/password/reset/{token}', '\App\Http\Controllers\Auth\ResetPasswordController@showResetForm')
             ->name('password.reset');
        Route::post('/password/reset', '\App\Http\Controllers\Auth\ResetPasswordController@reset');

        //Verificacao do usuario atraves do email
        Route::get('/user/verify/{token}', '\App\Http\Controllers\Auth\NewRegisterController@userVerifyMail')
             ->name('mail.verification');
    }
);

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


//Auth::routes();

