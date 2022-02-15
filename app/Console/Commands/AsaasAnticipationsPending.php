<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
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
    protected $signature = 'asaas:anticipations-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public $saveRequests = false;

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
        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {

            $service = new AsaasService();
            $sales = Sale::where([
                                     'status' => Sale::STATUS_APPROVED,
                                     'gateway_id' => Gateway::ASAAS_PRODUCTION_ID
                                 ])
                ->whereIn('anticipation_status', ['SCHEDULED','PENDING', 'ANTICIPATED_ASAAS', 'ANTICIPATED'])
                ->get();

            $total = count($sales);
            $bar = $this->output->createProgressBar($total);
            $bar->start();


            foreach ($sales as $sale) {

                $response = $service->checkAnticipation($sale, $this->saveRequests);

                if (isset($response['status'])) {
                    $sale->update(['anticipation_status' => $response['status']]);
                }

                $arrayStatus = ['SCHEDULED', 'PENDING', 'CREDITED', 'CANCELLED'];

                if (!isset($response['status']) or !in_array($response['status'], $arrayStatus)) {
                    report(new Exception("Erro ao consultar as antecipações, UserId:  " . $sale->owner_id . " SaleId:  " . $sale->id .
                                         ' -- ' . json_encode($response)));
                }

                $bar->advance();
            }

            $bar->finish();

        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));
    }
}
