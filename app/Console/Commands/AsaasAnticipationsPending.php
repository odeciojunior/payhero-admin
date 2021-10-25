<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\Gateways\AsaasService;

class AsaasAnticipationsPending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anticipations:asaas-pending';

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
     * @return void
     */
    public function handle()
    {
        $service = new AsaasService();
        $sales = Sale::where([
            'status' => Sale::STATUS_APPROVED,
            'gateway_id' => Gateway::ASAAS_PRODUCTION_ID,
            'anticipation_status' => 'PENDING',
        ])
            ->where('created_at', '>', '2021-10-19 00:00:00')
            ->get();

        foreach ($sales as $sale) {
           $response = $service->makeAnticipationSale($sale);

           if (isset($response->status)) {
               $sale->update(['anticipation_status', $response->status]);
           }
        }
    }
}
