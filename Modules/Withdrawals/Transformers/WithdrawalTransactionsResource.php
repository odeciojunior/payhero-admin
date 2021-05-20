<?php

namespace Modules\Withdrawals\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\GetnetBackOfficeService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class WithdrawalResource
 * @package Modules\Withdrawals\Transformers
 * @property string $transaction_code
 * @property boolean $liquidated
 * @property string $brand
 * @property string $date
 * @property string $value
 */
class WithdrawalTransactionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $isLiquidated = false;
        $date = '';
        $saleIdEncoded =Hashids::connection('sale_id')->encode($this->sale->id);


        //logica da getnet
        $getnetService = new GetnetBackOfficeService();

        if (FoxUtils::isProduction()) {
            $subsellerId = $this->company->subseller_getnet_id;
        } else {
            $subsellerId = $this->company->subseller_getnet_homolog_id;
        }

        $getnetService->setStatementSubSellerId($subsellerId)
            ->setStatementSaleHashId($this->sale->hash_id);

        $originalResult = $getnetService->getStatement();

        $gatewaySale = json_decode($originalResult);
        if (!empty($gatewaySale->list_transactions[0]) &&
            !empty($gatewaySale->list_transactions[0]->details[0]) &&
            !empty($gatewaySale->list_transactions[0]->details[0]->subseller_rate_confirm_date)
        ) {
            $isLiquidated = true;
            $date = str_replace('T', ' ', $gatewaySale->list_transactions[0]->details[0]->subseller_rate_confirm_date);
            $date = date("d/m/Y", strtotime($date));
            if(!$this->gateway_transferred) {
                $this->update([
                    'gateway_transferred' => 1
                ]);
            }
        }

        if(empty($this->sale->flag)){
            $this->sale->flag = $this->sale->present()->getPaymentFlag();
        }

        return [
            'transaction_code' => $saleIdEncoded,
            'liquidated'       => $isLiquidated,
            'brand'            => $this->sale->flag,
            'date'             => $date,
            'value'            => 'R$ ' . number_format(intval($this->value) / 100, 2, ',', '.'),

        ];
    }




}
