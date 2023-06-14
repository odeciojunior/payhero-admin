<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Entities\DiscountCoupon;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Presenters\ReportanaIntegrationPresenter;
use Modules\Core\Services\ReportanaService;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

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

Route::get("/", "\App\Http\Controllers\Auth\LoginController@showLoginForm");

Route::get("/up", function () {
    return 'System is up';
});

Route::get("/termos", function () {
    return response()->file(public_path("terms-of-use.pdf"));
});

Route::post("/reportana-update-sales", function () {
    $sales = Sale::where("status", Sale::STATUS_APPROVED)->whereIn("payment_method", [Sale::CREDIT_CARD_PAYMENT, Sale::PAYMENT_TYPE_BANK_SLIP, Sale::PAYMENT_TYPE_PIX])->whereDate("created_at", ">", "2023-05-07 00:00:00")->get();

    foreach ($sales as $sale) {
        $eventName = ReportanaIntegrationPresenter::getSearchEvent($sale->payment_method, $sale->status);

        echo $eventName . "<br>";

        // $reportanaService = new ReportanaService("https://api.reportana.com/2022-05/orders", 31);

        // $sale->load(["customer", "delivery", "plansSales.plan", "trackings"]);

        // $domain = Domain::where("status", 3)->where("project_id", $sale->project_id)->first();

        // $result = $reportanaService->sendSaleApi($sale, $sale->plansSales, $domain, $eventName);

        // echo json_encode($result["result"]) . "<br>";
    }
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

Route::group(
    [
        "prefix" => "dev",
        "as" => "dev.",
        "middleware" => ["web", "auth"],
        "namespace" => "\App\Http\Controllers\Dev",
    ],
    function () {
        // rotas autenticadas
        Route::get("/code/{code}", "TesteController@code");

        Route::get("/teste", "TesteController@index");

        Route::get("/julio", "JulioController@julioFunction");
        Route::get("/joao", "TesteController@joaoLucasFunction");
        Route::get("/thales", "TesteController@thalesFunction");
        Route::get("/jean", "TesteController@jeanFunction");
        Route::get("/fausto", "TesteController@faustoFunction");
        Route::get("/rmcharacter", "TesteController@removeSpecialCharacter");
        Route::get("/trackingcode", "TesteController@trackingCodeFunction");
        Route::get("/documentstatus", "TesteController@documentStatus");
        Route::get("/test-job-with-database", "TesteController@testJobWithDatabase");
    }
);

// utilitário para QA
// desabilitado em produção
if (env("APP_ENV", "production") !== "production") {
    Route::view("/qa-utils", "utils.info");
}

Route::get('/JeH8GqXkkPM7ZCNiI66GEpmU4MItRLkI/health', [Spatie\Health\Http\Controllers\HealthCheckResultsController::class, '__invoke']);
Route::get('/JeH8GqXkkPM7ZCNiI66GEpmU4MItRLkI/health-json', [Spatie\Health\Http\Controllers\HealthCheckJsonResultsController::class, '__invoke']);
