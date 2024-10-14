<?php

namespace Modules\Reports\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Entities\Tracking;
use Vinkla\Hashids\Facades\Hashids;

class TransactionBlockedResource extends JsonResource
{
    public function toArray($request)
    {
        $sale = $this->sale;

        if (empty($sale) || empty($sale->project)) {
            return null;
        }

        $data = [
            "sale_code" => "#" . Hashids::connection("sale_id")->encode($sale->id),
            "id" => Hashids::connection("sale_id")->encode($sale->id),
            "upsell" => Hashids::connection("sale_id")->encode($this->sale->upsell_id),
            "project" => $sale->project->name,
            "product" =>
                count($sale->getRelation("plansSales")) > 1 ? "Carrinho" : $sale->plansSales->first()->plan->name,
            "client" => $sale->customer->name,
            "method" => $sale->payment_method,
            "status" => $sale->status,
            "status_translate" => Lang::get(
                "definitions.enum.sale.status." . $sale->present()->getStatus($sale->status)
            ),
            "start_date" => $sale->start_date ? Carbon::parse($sale->start_date)->format("d/m/Y H:i:s") : "",
            "end_date" => $sale->end_date ? Carbon::parse($sale->end_date)->format("d/m/Y H:i:s") : "",
            "total_paid" => 'R$ ' . substr_replace(@$this->value, ",", strlen(@$this->value) - 2, 0),
            "brand" => !empty($sale->flag) ? $sale->flag : $this->sale->present()->getPaymentFlag(),
        ];

        // if($sale->status == 24) {
        //     $data['reason_blocked'] = 'Em disputa';
        // } elseif($sale->tracking->count() < $sale->productsPlansSale->count()){
        //     $data['reason_blocked'] = 'Sem rastreio';
        // } elseif($sale->tracking->where('system_status_enum', (new Tracking())->present()->getSystemStatusEnum('duplicated'))->count()) {
        //     $data['reason_blocked'] = 'Já existe uma venda com o código de rastreio informado';
        // } elseif($sale->tracking->where('system_status_enum', (new Tracking())->present()->getSystemStatusEnum('no_tracking_info'))->count()) {
        //     $data['reason_blocked'] = 'Rastreio sem movimentação';
        // } else {
        //     $data['reason_blocked'] = 'Motivo não listado';
        // }
        $reasonBlock = "";
        foreach ($this->blockReasonSale as $blockReasonSale) {
            if (!empty($reasonBlock)) {
                $reasonBlock .= ", ";
            }
            $reasonBlock .= $blockReasonSale->observation;
        }
        $data["reason_blocked"] = $reasonBlock;

        if ($sale->owner_id == auth()->user()->account_owner_id) {
            $data["user_sale_type"] = "producer";
        } else {
            $data["user_sale_type"] = "affiliate";
        }

        if (!empty($sale->affiliate->id)) {
            $data["affiliate"] = $sale->affiliate->user->name;
        } else {
            $data["affiliate"] = null;
        }

        return $data;
    }
}
