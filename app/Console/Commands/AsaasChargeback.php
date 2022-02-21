<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\SaleService;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class AsaasChargeback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asaas:chargeback';

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

    public function handle()
    {
        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {

            $getnetChargebacks = null;

            $getnetChargebacks = Sale::whereDoesntHave('pendingDebts')
                ->whereHas('transactions', function($q) {
                    $q->whereDoesntHave('transfers', function($qu) {
                        $qu->where('reason', 'chargedback');
                    });
                    $q->whereNotNull('company_id');
                })
                ->where('gateway_id', Gateway::GETNET_PRODUCTION_ID)
                ->where('status', Sale::STATUS_CHARGEBACK)
                ->with('saleLogs')
                ->get();

            $totalValue = 0;

            $output = new ConsoleOutput();
            $progress = new ProgressBar($output, count($getnetChargebacks));
            $progress->start();

            $saleService = new SaleService();

            foreach($getnetChargebacks as $sale) {

                $progress->advance();

                $cashbackValue = !empty($sale->cashback) ? $sale->cashback->value:0;
                $saleTax = $saleService->getSaleTax($sale,$cashbackValue);

                foreach ($sale->transactions as $transaction) {
                    if (empty($transaction->company)) {
                        continue;
                    }

                    $chargebackValue = $transaction->value;
                    if ($transaction->type == Transaction::TYPE_PRODUCER) {
                        if (!empty($transaction->sale->automatic_discount)) {
                            $chargebackValue -= $transaction->sale->automatic_discount;
                        }
                        $chargebackValue += $saleTax;
                    }

                    $company = $transaction->company;

                    $company->update([
                                         'asaas_balance' => $company->asaas_balance -= $chargebackValue
                                     ]);

                    Transfer::create(
                        [
                            'user_id' => $company->user_id,
                            'company_id' => $company->id,
                            'transaction_id' => $transaction->id,
                            'value' => $chargebackValue,
                            'type' => 'out',
                            'type_enum' => Transfer::TYPE_OUT,
                            'reason' => 'chargedback',
                            'gateway_id' => foxutils()->isProduction() ? Gateway::ASAAS_PRODUCTION_ID : Gateway::ASAAS_SANDBOX_ID,
                        ]
                    );

                    $totalValue += $chargebackValue;
                }

                $this->line($sale->id . ' - chargeback criado com sucesso');
            }

            $progress->finish();

            $this->line("Valor total {$totalValue}");

        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));
    }

}
