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
    //return view('welcome');
    return view('auth.login');
});

Route::get('/liftgold', function (Request $request) {
echo 'to aqui';die;
    $dados = $request->all();

    Log::write('info', 'retorno liftgold : '.print_r($dados,true));    
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//Route::redirect("home", "cliente", 301);
//Route::view("welcome", "welcome");
