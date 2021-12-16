<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
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

        foreach($getnetChargebacks as $sale) {

            $progress->advance();

            $cloudfoxTransaction = $sale->transactions()->whereNull('company_id')->first();
            $saleTax = $this->getSaleTax($cloudfoxTransaction, $sale);

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
    }

    private function getSaleTax($cloudfoxTransaction, $sale)
    {
        $saleTax = $cloudfoxTransaction->value;
        if (!empty($sale->installment_tax_value)) {
            $saleTax -= $sale->installment_tax_value;
        } elseif ($sale->installments_amount > 1) {
            $saleTax -= ($sale->original_total_paid_value -
                (
                    foxutils()->onlyNumbers($sale->sub_total) +
                    foxutils()->onlyNumbers($sale->shipment_value)
                ));
            if (!empty(foxutils()->onlyNumbers($sale->shopify_discount))) {
                $saleTax -= foxutils()->onlyNumbers($sale->shopify_discount);
            }
        }

        return $saleTax;
    }

}
