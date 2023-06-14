<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Sale;
use Modules\Core\Presenters\ReportanaIntegrationPresenter;
use Modules\Core\Services\ReportanaService;

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

Route::post("/reportana-update-sales", function () {
    try {
        $sales = Sale::where("status", Sale::STATUS_APPROVED)->whereIn("payment_method", [Sale::CREDIT_CARD_PAYMENT, Sale::PAYMENT_TYPE_BANK_SLIP, Sale::PAYMENT_TYPE_PIX])->whereDate("created_at", ">", "2023-05-07 00:00:00")->orderBy("id", "desc")->get();

        foreach ($sales as $sale) {
            $sale->load(["customer", "delivery", "plansSales.plan", "trackings"]);

            $domain = Domain::where("status", 3)->where("project_id", $sale->project_id)->first();

            $eventName = ReportanaIntegrationPresenter::getSearchEvent($sale->payment_method, $sale->status);

            $reportanaService = new ReportanaService("https://api.reportana.com/2022-05/orders", 31);

            $result = $reportanaService->sendSaleApi($sale, $sale->plansSales, $domain, $eventName);

            echo json_encode($result["result"]) . "<br><br>";
        }
    } catch (Exception $e) {
        report($e);
    }
});
