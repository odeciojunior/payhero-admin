<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Presenters\ReportanaIntegrationPresenter;
use Modules\Core\Services\ReportanaService;

class UpdateSaleReportana extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reportana:update-sales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command para atualizar os status dos pedidos no Reportana da Nexus.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $sales = Sale::where("status", Sale::STATUS_APPROVED)->whereDate("created_at", ">", "2022-05-07 00:00:00")->get();
            foreach ($sales as $sale) {
                if ($sale->api_flag) {
                    return;
                }

                $isShopify = ShopifyIntegration::where("project_id", $sale->project_id)->where("status", ShopifyIntegration::STATUS_APPROVED)->exists();

                if (!$isShopify || $sale->shopify_order || in_array($sale->status, [Sale::STATUS_REFUSED, Sale::STATUS_CANCELED_ANTIFRAUD]) || $sale->reportana_recovery_flag) {
                    $reportanaService = new ReportanaService("https://api.reportana.com/2022-05/orders", 31);

                    $sale->load(["customer", "delivery", "plansSales.plan", "trackings"]);

                    $domain = Domain::where("status", 3)->where("project_id", $sale->project_id)->first();

                    $eventName = ReportanaIntegrationPresenter::getSearchEvent($sale->payment_method, $sale->status);

                    $reportanaService->sendSaleApi($sale, $sale->plansSales, $domain, $eventName);
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
