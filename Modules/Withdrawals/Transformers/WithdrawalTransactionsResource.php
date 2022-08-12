<?php

namespace Modules\Withdrawals\Transformers;

use Carbon\Carbon;
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
        $date = "";
        $saleIdEncoded = Hashids::connection("sale_id")->encode($this->sale->id);

        if (!empty($this->gateway_transferred_at)) {
            $isLiquidated = true;
            $date = with(new Carbon($this->gateway_transferred_at))->format("d/m/Y");
        }
        if (empty($this->sale->flag)) {
            $this->sale->flag = $this->sale->present()->getPaymentFlag();
        }

        return [
            "transaction_code" => $saleIdEncoded,
            "liquidated" => $isLiquidated,
            "brand" => $this->sale->flag,
            "date" => $date,
            "value" => 'R$ ' . number_format(intval($this->value) / 100, 2, ",", "."),
        ];
    }
}
