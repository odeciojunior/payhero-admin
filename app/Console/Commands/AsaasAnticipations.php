<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\Gateways\AsaasService;

class AsaasAnticipations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anticipations:asaas';

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
        try {

            $service = new AsaasService();

            $toDay = Carbon::now();

            $transactions = Transaction::with('sale')
                ->whereHas('sale', function ($query)  {
                    $query->whereNull('anticipation_status');
                    $query->where('payment_method', Sale::CREDIT_CARD_PAYMENT);
                })
                ->where('gateway_id', Gateway::ASAAS_PRODUCTION_ID)
                ->where('status_enum', Transaction::STATUS_PAID)
                ->whereNotNull('company_id')
                ->where('release_date', '<=', $toDay->addDays(3)->format("Y-m-d"));

            foreach ($transactions->cursor() as $transaction) {
                $sale = $transaction->sale;
                $response = $service->makeAnticipation($sale);

                if (isset($response['status'])) {
                    $sale->update([
                        'anticipation_status' => $response['status'],
                        'anticipation_id' => $response['id']
                    ]);
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
