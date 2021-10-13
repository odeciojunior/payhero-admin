<?php

namespace App\Console\Commands;

use Exception;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Entities\GetnetChargeback;
use Modules\Core\Entities\PendingDebt;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleContestation;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Services\AdjustmentRequest;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Core\Services\SaleService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $chargebacks = GetnetChargeback::with('sale')
                                    ->whereHas('sale', function($q) {
                                        $q->where('id', 1167532);
                                    })
                                    ->get();

        foreach($chargebacks as $chargeback) {
            $this->createDebit($chargeback->sale);
        }

        $this->line('total: ' . count($chargebacks));
    }

    function fixChargeback(Sale $sale) {
        DB::beginTransaction();

        try {
            $sale->status = Sale::STATUS_CHARGEBACK;
            $sale->save();

            Redis::connection('redis-statement')->set("sale:has:tracking:{$sale->id}", true);

            SaleService::createSaleLog($sale->id, 'charge_back');

            $getnetService = new GetnetBackOfficeService();

            foreach ($sale->transactions as $transaction) {
                if (!$transaction->is_waiting_withdrawal && !empty($transaction->company_id)) {

                    $this->line(CompanyService::getSubsellerId($transaction->company));
                    $orderId = $sale->gateway_order_id;

                    $response = $getnetService->setStatementSaleHashId(hashids_encode($sale->id, 'sale_id'))
                        ->setStatementSubSellerId(CompanyService::getSubsellerId($transaction->company))
                        ->getStatement($orderId);

                    $gatewaySale = json_decode($response);

                    if (
                        !empty($gatewaySale->list_transactions[0]) &&
                        !empty($gatewaySale->list_transactions[0]->details[0]) &&
                        empty($gatewaySale->list_transactions[0]->details[0]->subseller_rate_confirm_date)
                    ) {
                        $transaction->is_waiting_withdrawal = true;
                        $transaction->save();
                    }
                }

                $transaction->status = 'chargeback';
                $transaction->status_enum = Transaction::STATUS_CHARGEBACK;
                $transaction->save();
            }

            $sale_contestation = $sale->contestations()->first();

            if (!empty($sale_contestation)) {
                $sale_contestation->update([
                    'status' => SaleContestation::STATUS_LOST
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            report($e);
        }
    }






    function createDebit(Sale $sale) 
    {
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
