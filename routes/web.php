<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get("/", [LoginController::class, 'showLoginForm'])
    ->name('login-form');

Route::get("/up", function () {
    return "System is up";
});

// Whitelabel routes
Route::get('/whitelabel/css', '\App\Http\Controllers\WhitelabelController@css')
    ->name('whitelabel.css');
Route::post('/whitelabel/clear-cache', '\App\Http\Controllers\WhitelabelController@clearCache')
    ->name('whitelabel.clear-cache')
    ->middleware('auth');

Route::get("/account-validation/{user_id}", function () {
    return view("idwall.face-id")->with("user_id", request()->user_id);
});
Route::post("/validate-user", "\App\Http\Controllers\Idwall\UserValidationController@validateUser");

Route::get("/termos", function () {
    return response()->file(public_path("terms-of-use.pdf"));
});

Route::group([], function () {
    // rotas autenticadas
    // rotas para autenticação e registro de novos usuarios
    Route::get("/login", "\App\Http\Controllers\Auth\LoginController@showLoginForm")->name("login");
    Route::post("/login", "\App\Http\Controllers\Auth\LoginController@login");

    Route::post("/logout", "\App\Http\Controllers\Auth\LoginController@logout")->name("logout");
    //somente para desenvolvimento, depois remover e deixar somente o metodo post para logout
    Route::get("logout", "\App\Http\Controllers\Auth\LoginController@logout");

    //rotas para login automatico
    Route::get("/send-authenticated", "\App\Http\Controllers\Auth\LoginController@sendAuthenticated");
    Route::get("/get-authenticated/{user}/{expiration}", "\App\Http\Controllers\Auth\LoginController@getAuthenticated");

    // Registration Routes...
    //        Route::get('/register', '\App\Http\Controllers\Auth\NewRegisterController@index')->name('register');
    //        Route::post('/register', '\App\Http\Controllers\Auth\NewRegisterController@store');

    // Password Reset Routes...
    //        Route::get('/password/reset', '\App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')
    //             ->name('password.request');

    Route::post("/password/email", "\App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail")->name(
        "password.email"
    );
    Route::get("/password/reset/{token}", "\App\Http\Controllers\Auth\ResetPasswordController@showResetForm")->name(
        "password.reset"
    );
    Route::post("/password/reset", "\App\Http\Controllers\Auth\ResetPasswordController@reset")->name(
        "password.reset.post"
    );

    //Verificacao do usuario atraves do email
    // Route::get('/user/verify/{token}', '\App\Http\Controllers\Auth\NewRegisterController@userVerifyMail')
    //      ->name('mail.verification');
});


// utilitário para QA
// desabilitado em produção
if (env("APP_ENV", "production") !== "production") {
    Route::view("/qa-utils", "utils.info");
}

Route::get("/JeH8GqXkkPM7ZCNiI66GEpmU4MItRLkI/health", [
    Spatie\Health\Http\Controllers\HealthCheckResultsController::class,
    "__invoke",
]);
Route::get("/JeH8GqXkkPM7ZCNiI66GEpmU4MItRLkI/health-json", [
    Spatie\Health\Http\Controllers\HealthCheckJsonResultsController::class,
    "__invoke",
]);
