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
    protected $signature = 'reportana:update-sales {paymentMethod?}';

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
            $paymentMethod = $this->argument('paymentMethod');

            $paymentMethodArray = !empty($paymentMethod) ? [$paymentMethod] : [Sale::CREDIT_CARD_PAYMENT, Sale::PAYMENT_TYPE_BANK_SLIP, Sale::PAYMENT_TYPE_PIX];

            $sales = Sale::where("status", Sale::STATUS_APPROVED)->whereIn("payment_method", $paymentMethodArray)->whereDate("created_at", ">", "2023-05-07 00:00:00")->get();
            foreach ($sales as $sale) {
                if ($sale->api_flag) {
                    return;
                }

                $reportanaService = new ReportanaService("https://api.reportana.com/2022-05/orders", 31);

                $sale->load(["customer", "delivery", "plansSales.plan", "trackings"]);

                $domain = Domain::where("status", 3)->where("project_id", $sale->project_id)->first();

                $eventName = ReportanaIntegrationPresenter::getSearchEvent($sale->payment_method, $sale->status);

                $result = $reportanaService->sendSaleApi($sale, $sale->plansSales, $domain, $eventName);

                $this->line(json_encode($result["result"]));
                $this->newLine();
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
