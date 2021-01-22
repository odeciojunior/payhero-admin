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

        $orderId = $this->sale->gateway_order_id;

        if (FoxUtils::isProduction()) {
            $subsellerId = $this->company->subseller_getnet_id;
        } else {
            $subsellerId = $this->company->subseller_getnet_homolog_id;
        }

        $response = $getnetService->getStatementFromManager(
            [
                'order_id' => $orderId,
                'subseller_id' => $subsellerId
            ]
        );

        $gatewaySale = json_decode($response);
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

        if ($this->sale->flag) {
            $this->sale->flag = $this->sale->flag;
        } else if ((!$this->sale->flag || empty($this->sale->flag)) && $this->sale->payment_method == 1) {
            $this->sale->flag = 'generico';
        } else if ((!$this->sale->flag || empty($this->sale->flag)) && $this->sale->payment_method == 3) {
            $this->sale->flag = 'debito';
        } else {
            $this->sale->flag = 'boleto';
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
