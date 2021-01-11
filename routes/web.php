<?php

use Illuminate\Support\Facades\Route;

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
//Route::post('/guard/broadcast/auth', function(\Illuminate\Support\Facades\Request $req) {
//    return true;
//})->middleware('broadcast')->name('broadcast.auth');


Route::get('/', '\App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');

Route::get('/termos', function () {
    return response()->file(public_path('terms-of-use.pdf'));
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

        //rotas para login automatico
        Route::get('/send-authenticated', '\App\Http\Controllers\Auth\LoginController@sendAuthenticated');
        Route::get('/get-authenticated/{user}/{expiration}', '\App\Http\Controllers\Auth\LoginController@getAuthenticated');

        // Registration Routes...
        //        Route::get('/register', '\App\Http\Controllers\Auth\NewRegisterController@index')->name('register');
        //        Route::post('/register', '\App\Http\Controllers\Auth\NewRegisterController@store');

        // Password Reset Routes...
        //        Route::get('/password/reset', '\App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')
        //             ->name('password.request');

        Route::post('/password/email', '\App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')
             ->name('password.email');
        Route::get('/password/reset/{token}', '\App\Http\Controllers\Auth\ResetPasswordController@showResetForm')
             ->name('password.reset');
        Route::post('/password/reset', '\App\Http\Controllers\Auth\ResetPasswordController@reset')
             ->name('password.reset.post');

        //Verificacao do usuario atraves do email
        // Route::get('/user/verify/{token}', '\App\Http\Controllers\Auth\NewRegisterController@userVerifyMail')
        //      ->name('mail.verification');
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
        Route::get('/code/{code}', 'TesteController@code');

        Route::get('/teste', 'TesteController@index');

        Route::get('/julio', 'JulioController@julioFunction');
        Route::get('/joao', 'TesteController@joaoLucasFunction');
        Route::get('/thales', 'TesteController@thalesFunction');
        Route::get('/jean', 'TesteController@jeanFunction');
        Route::get('/fausto', 'TesteController@faustoFunction');
        Route::get('/rmcharacter', 'TesteController@removeSpecialCharacter');
        Route::get('/trackingcode', 'TesteController@trackingCodeFunction');
        Route::get('/wilson', 'WilsonController@wilsonFunction');
        Route::get('/documentstatus', 'TesteController@documentStatus');
        Route::get('/test-job-with-database', 'TesteController@testJobWithDatabase');
    }
);

Route::view('/carriers', 'carriers');

Route::get('/generate-zend-jwt', 'HomeController@generateZendesktoken');
