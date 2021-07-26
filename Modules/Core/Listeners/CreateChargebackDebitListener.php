<?php

namespace Modules\Core\Listeners;

use Exception;
use Modules\Core\Entities\PendingDebt;
use Modules\Core\Entities\Transaction;
use Modules\Core\Events\NewChargebackEvent;
use Modules\Core\Services\AdjustmentRequest;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\GetnetBackOfficeService;

class CreateChargebackDebitListener
{
    public function __construct()
    {
        //
    }

    public function handle(NewChargebackEvent $event)
    {
        $sale = $event->sale;
        $cloudfoxTransaction = $sale->transactions()->whereNull('company_id')->first();
        $inviteTransaction = $sale->transactions()->whereNotNull('invitation_id')->first();
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
                if (!empty($inviteTransaction)) {
                    $chargebackValue += $inviteTransaction->value;
                }
            }

            $company = $transaction->company;
            $getnetKeys = $this->getnetKeys();
            $merchantId = $getnetKeys['merchantId'];
            $sellerId = $getnetKeys['sellerId'];

            $hasPendingDebt = PendingDebt::where('sale_id', $sale->id)
                ->where('company_id', $company->id)
                ->count();

            if ($hasPendingDebt >= 1) {
                $e = new Exception('Já existe debito pendente para esta venda: ' . $sale->id);
                report($e);
            } else {
                $adjustment = new AdjustmentRequest();
                $adjustment->setAmount($chargebackValue)
                    ->setSaleId($sale->id)
                    ->setCompanyId($company->id)
                    ->setDescription('Chargeback da venda #' . hashids_encode($sale->id, 'sale_id'))
                    ->setMerchantId($merchantId)
                    ->setSellerId($sellerId)
                    ->setSubSellerId(CompanyService::getSubsellerId($company))
                    ->setTypeAdjustment(AdjustmentRequest::DEBIT_ADJUSTMENT);

                (new GetnetBackOfficeService())->requestAdjustment($adjustment);
            }
        }
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
