<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Http\Controllers\DashboardApiController;
use Modules\Mobile\Http\Controllers\MobileController;
use Modules\Withdrawals\Http\Controllers\WithdrawalsApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['auth:api','demo_account'], 'prefix' => 'mobile'], function() {
    Route::get('/balances', [DashboardApiController::class, 'getValues'])->name('mobile.balances');
    Route::get('/sales', [MobileController::class, 'sales'])->name('mobile.sales');
    Route::get('/withdrawals', [WithdrawalsApiController::class, 'index'])->name('mobile.withdrawals');
    Route::post('/withdrawals', [WithdrawalsApiController::class, 'store'])->name('mobile.withdrawals.store');
    Route::get('/statements-resume', [MobileController::class, 'statementsResume'])->name('mobile.statements-resume');
});
