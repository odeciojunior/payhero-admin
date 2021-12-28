<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\PendingDebt;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Events\NewChargebackEvent;
use Modules\Core\Services\AdjustmentRequest;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\GetnetBackOfficeService;
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
        try{

            $sale = $event->sale;
            
            $saleService = new SaleService();
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

            }
        }
        catch(Exception $e) {
            report($e);
        }

    }


    // public function handle(NewChargebackEvent $event)    GETNET
    // {
    //     $sale = $event->sale;
    //     $cloudfoxTransaction = $sale->transactions()->whereNull('company_id')->first();
    //     $inviteTransaction = $sale->transactions()->whereNotNull('invitation_id')->first();
    //     $saleTax = $this->getSaleTax($cloudfoxTransaction, $sale);

    //     foreach ($sale->transactions as $transaction) {
    //         if (empty($transaction->company)) {
    //             continue;
    //         }

    //         $chargebackValue = $transaction->value;
    //         if ($transaction->type == Transaction::TYPE_PRODUCER) {
    //             if (!empty($transaction->sale->automatic_discount)) {
    //                 $chargebackValue -= $transaction->sale->automatic_discount;
    //             }
    //             $chargebackValue += $saleTax;
    //             if (!empty($inviteTransaction)) {
    //                 $chargebackValue += $inviteTransaction->value;
    //             }
    //         }

    //         $company = $transaction->company;
    //         $getnetKeys = $this->getnetKeys();
    //         $merchantId = $getnetKeys['merchantId'];
    //         $sellerId = $getnetKeys['sellerId'];

    //         $hasPendingDebt = PendingDebt::where('sale_id', $sale->id)
    //             ->where('company_id', $company->id)
    //             ->count();

    //         if ($hasPendingDebt >= 1) {
    //             $e = new Exception('Já existe debito pendente para esta venda: ' . $sale->id);
    //             report($e);
    //         } else {
    //             $adjustment = new AdjustmentRequest();
    //             $adjustment->setAmount($chargebackValue)
    //                 ->setSaleId($sale->id)
    //                 ->setCompanyId($company->id)
    //                 ->setDescription('Chargeback da venda #' . hashids_encode($sale->id, 'sale_id'))
    //                 ->setMerchantId($merchantId)
    //                 ->setSellerId($sellerId)
    //                 ->setSubSellerId(CompanyService::getSubsellerId($company))
    //                 ->setTypeAdjustment(AdjustmentRequest::DEBIT_ADJUSTMENT);

    //             (new GetnetBackOfficeService())->requestAdjustment($adjustment);
    //         }
    //     }
    // }


    private function getnetKeys(): array
    {
        if (foxutils()->isProduction()) {
            return [
                'merchantId' => env('GET_NET_MERCHANT_ID_PRODUCTION'),
                'sellerId' => env('GET_NET_SELLER_ID_PRODUCTION'),
            ];
        }

        return [
            'merchantId' => env('GET_NET_MERCHANT_ID_SANDBOX'),
            'sellerId' => env('GET_NET_SELLER_ID_SANDBOX'),
        ];
    }
}
