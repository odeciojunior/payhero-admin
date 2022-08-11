<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Events\NewChargebackEvent;
use Modules\Core\Services\SaleService;

class CreateChargebackDebitListener implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function handle(NewChargebackEvent $event)
    {
        try {
            $sale = $event->sale;

            $saleService = new SaleService();
            $cashbackValue = !empty($sale->cashback) ? $sale->cashback->value : 0;
            $saleTax = $saleService->getSaleTax($sale, $cashbackValue);

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

                Transfer::create([
                    "user_id" => $company->user_id,
                    "company_id" => $company->id,
                    "transaction_id" => $transaction->id,
                    "value" => $chargebackValue,
                    "type" => "out",
                    "type_enum" => Transfer::TYPE_OUT,
                    "reason" => "chargedback",
                    "gateway_id" => foxutils()->isProduction()
                        ? Gateway::SAFE2PAY_PRODUCTION_ID
                        : Gateway::SAFE2PAY_SANDBOX_ID,
                ]);

                $company->update([
                    "safe2pay_balance" => $company->safe2pay_balance - $chargebackValue,
                ]);
            }
        } catch (Exception $e) {
            report($e);
        }
    }

    private function getnetKeys(): array
    {
        if (foxutils()->isProduction()) {
            return [
                "merchantId" => env("GET_NET_MERCHANT_ID_PRODUCTION"),
                "sellerId" => env("GET_NET_SELLER_ID_PRODUCTION"),
            ];
        }

        return [
            "merchantId" => env("GET_NET_MERCHANT_ID_SANDBOX"),
            "sellerId" => env("GET_NET_SELLER_ID_SANDBOX"),
        ];
    }
}
