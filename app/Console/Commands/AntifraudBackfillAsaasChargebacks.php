<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Modules\Core\Entities\AntifraudWarning;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleContestation;
use Modules\Core\Services\Antifraud\CloudfoxAntifraudService;

class AntifraudBackfillAsaasChargebacks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'antifraud:backfill-asaas-chargebacks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $salesQuery = Sale::query()
            ->select('sales.*')
            ->with('saleInformations')
            ->join('sale_contestations', 'sale_id', '=', 'sales.id')
            ->where('sale_contestations.gateway_id', Gateway::ASAAS_PRODUCTION_ID)
            ->where('sale_contestations.status', SaleContestation::STATUS_LOST)
            ->where('sales.status', Sale::STATUS_CHARGEBACK)
            ->whereNotIn(
                'sales.id',
                function ($query) {
                    $query->select('sale_id')->from('antifraud_warnings')->where(
                        'status',
                        AntifraudWarning::STATUS_FRAUD_CONFIRMED
                    );
                }
            );

        $antifraudService = new CloudfoxAntifraudService();
        foreach ($salesQuery->get() as $sale) {
            $this->line($sale->id);
            $antifraudService->updateConfirmedFraudData($sale);
        }
        return 0;
    }
}
