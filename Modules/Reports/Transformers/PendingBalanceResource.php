<?php

namespace Modules\Reports\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;

class PendingBalanceResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $sale = $this->sale;

        $data = [
            "id" => Hashids::connection("sale_id")->encode($sale->id),
            "sale_code" => "#" . Hashids::connection("sale_id")->encode($sale->id),
            "brand" => !empty($sale->flag) ? $sale->flag : $this->sale->present()->getPaymentFlag(),
            "project" => $sale->project->name ?? "",
            "client" => $sale->customer->name ?? "",
            "start_date" => $sale->start_date ? Carbon::parse($sale->start_date)->format("d/m/Y H:i:s") : "",
            "end_date" => $sale->end_date ? Carbon::parse($sale->end_date)->format("d/m/Y H:i:s") : "",
            "total_paid" => 'R$ ' . substr_replace(@$this->value, ",", strlen(@$this->value) - 2, 0),
            "is_security_reserve" => intval($this->is_security_reserve),
        ];

        return $data;
    }
}
