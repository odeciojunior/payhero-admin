<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\AccountApprovedService;
use Modules\Core\Services\FoxUtils;

use Modules\Core\Services\Gateways\AsaasService;

use function PHPUnit\Framework\isEmpty;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        try {

            $service = new AsaasService();

            $transactions = Transaction::with('sale')
                ->whereHas('sale', function ($query)  {
                    $query->whereNull('anticipation_status');
                    $query->where('payment_method', Sale::CREDIT_CARD_PAYMENT);
                })
                ->where('gateway_id', Gateway::ASAAS_PRODUCTION_ID)
                ->whereIn('status_enum', [Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED])
                ->whereNotNull('company_id')
                ->whereBetween('release_date',  ['2021-11-01', '2021-11-24']);

            foreach ($transactions->cursor() as $transaction) {
                $sale = $transaction->sale;
                $this->line("Sale_id: ". $sale->id . ', ');
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
