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

            $toDay = Carbon::now()->format("Y-m-d");
            $afterThreeDays =  Carbon::now()->addDays(3)->format("Y-m-d");

            $transactions = Transaction::with('sale')
                ->whereHas('sale', function ($query)  {
                    $query->whereNull('anticipation_status');
                    $query->where('payment_method', Sale::CREDIT_CARD_PAYMENT);
                })
                ->where('gateway_id', Gateway::ASAAS_PRODUCTION_ID)
                ->whereIn('status_enum', [Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED])
                ->whereNotNull('company_id')
                ->where('type', Transaction::TYPE_PRODUCER)
                ->where('release_date', '<=', $afterThreeDays)
                ->where('created_at', '<', $toDay);

            $cannotAnticipate = [];
            foreach ($transactions->cursor() as $transaction) {
                $sale = $transaction->sale;
                $response = $service->makeAnticipation($sale);

                if (isset($response['status'])) {
                    $sale->update([
                                      'anticipation_status' => $response['status'],
                                      'anticipation_id' => $response['id']
                                  ]);
                }

                if(isset($response['errors'][0]['code'])
                    and ($response['errors'][0]['code'] == 'cannotAnticipate' or $response['errors'][0]['code'] == 'invalid_action')
                    and (str_contains($response['errors'][0]['description'], 'Este recebível já está reservado para a instituição') ) ) {

                    $transaction->user->update([
                        'asaas_alert' => true
                    ]);
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
