<?php

declare(strict_types=1);

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

Route::group(["middleware" => ["web", "auth", "permission:apps"]], function () {
    Route::resource("/integrations", "IntegrationsController")
        ->only("index")
        ->names("integrations");
});
