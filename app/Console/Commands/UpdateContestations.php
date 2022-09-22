<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleContestation;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\SaleService;

class UpdateContestations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contestations:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = [];

        $transactions = Transaction::with(['user', 'sale'])
                                    ->where("gateway_id", Gateway::SAFE2PAY_PRODUCTION_ID)
                                    ->where('type', Transaction::TYPE_PRODUCER)
                                    ->whereIn("status_enum", [Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED])
                                    ->join("block_reason_sales", "block_reason_sales.sale_id", "=", "transactions.sale_id")
                                    ->where("block_reason_sales.status", BlockReasonSale::STATUS_BLOCKED)
                                    ->where("block_reason_sales.blocked_reason_id", 1)
                                    ->get();

        foreach($transactions as $transaction) {
            $users[$transaction->user->name] ??= 0;
            if($users[$transaction->user->name] + $transaction->value < 2000000) {
                $users[$transaction->user->name] += $transaction->value;
                $this->processChargebackLost($transaction->sale);
            }
        }
    }

    public function processChargebackLost($sale)
    {

        $this->line("===========>>>>>>>    PROCESSANDO CHARGEBACK DA VENDA " . hashids_encode($sale->id, 'sale_id'));
        try {
            if ($sale->status != Sale::STATUS_APPROVED) {
                $this->line("Venda aprovada");
                return;
            }

            $saleContestation = SaleContestation::where("sale_id", $sale->id)->first();
            if (empty(!$saleContestation)) {
                $saleContestation->update([
                    "status" => SaleContestation::STATUS_LOST,
                ]);
                $this->line("Vai atualizar a contestação pra perdida");
            }

            $blockSales = BlockReasonSale::where("sale_id", $sale->id)->get();
            if (!empty($blockSales)) {
                foreach($blockSales as $blockSale) {
                    $blockSale->update([
                        "status" => BlockReasonSale::STATUS_UNLOCKED,
                    ]);
                    $this->line("Vai desbloquear o valor da venda");
                }
            }

            $chargebackTransactions = $sale->transactions;

            $saleService = new SaleService();

            $cashbackValue = !empty($sale->cashback->value) ? $sale->cashback->value : 0;
            $saleTax = $saleService->getSaleTaxRefund($sale, $cashbackValue);

            $safe2payBalance = 0;
            foreach ($chargebackTransactions as $chargebackTransaction) {
                $company = $chargebackTransaction->company;
                if (!empty($company)) {
                    $safe2payBalance = $company->safe2pay_balance;

                    $chargebackValue = $chargebackTransaction->value;
                    if ($chargebackTransaction->type == Transaction::TYPE_PRODUCER) {
                        $chargebackValue += $saleTax;
                    }

                    if ($chargebackTransaction->status_enum != Transaction::STATUS_TRANSFERRED) {
                        $safe2payBalance += $chargebackTransaction->value;
                        Transfer::create([
                            "transaction_id" => $chargebackTransaction->id,
                            "user_id" => $company->user_id,
                            "company_id" => $company->id,
                            "type_enum" => Transfer::TYPE_IN,
                            "value" => $chargebackTransaction->value,
                            "type" => "in",
                            "gateway_id" => Gateway::SAFE2PAY_PRODUCTION_ID
                        ]);

                        $company->update([
                            "safe2pay_balance" => $safe2payBalance,
                        ]);
                        $this->line("Vai transferir o dinheiro da venda");
                    }

                    Transfer::create([
                        "transaction_id" => $chargebackTransaction->id,
                        "user_id" => $chargebackTransaction->user_id,
                        "company_id" => $chargebackTransaction->company_id,
                        "gateway_id" => Gateway::SAFE2PAY_SANDBOX_ID,
                        "value" => $chargebackValue,
                        "type" => "out",
                        "type_enum" => Transfer::TYPE_OUT,
                        "reason" => "chargedback",
                        "is_refunded_tax" => 0,
                    ]);

                    $company->update([
                        "safe2pay_balance" => $safe2payBalance - $chargebackValue,
                    ]);
                    $this->line("Vai lançar o chargeback");
                }

                $chargebackTransaction->status = "chargedback";
                $chargebackTransaction->status_enum = Transaction::STATUS_CHARGEBACK;
                $chargebackTransaction->save();
                $this->line("Vai atualizar a transaction");
            }

            $sale->update([
                "status" => Sale::STATUS_CHARGEBACK,
                "gateway_status" => "CHARGEBACK",
            ]);

            SaleService::createSaleLog($sale->id, Sale::STATUS_CHARGEBACK);
            $this->line("Vai atualizar a venda");
        } catch (Exception $ex) {
            report($ex);
        }
    }
}
