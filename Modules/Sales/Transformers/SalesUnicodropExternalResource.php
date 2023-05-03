<?php

namespace Modules\Sales\Transformers;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\PixCharge;

/**
 * Class SalesExternalResource
 * @package Modules\Sales\Transformers
 */
class SalesUnicodropExternalResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $amount = preg_replace("/[^0-9]/", "", $this->details->total) / 100;

        $netAmount = preg_replace("/[^0-9]/", "", $this->details->comission) / 100;

        $fee = preg_replace("/[^0-9]/", "", $this->details->taxaReal) / 100;
        $fee += preg_replace("/[^0-9]/", "", $this->installment_tax_value ?? 0) / 100;

        $pixCharge = PixCharge::where("sale_id", $this->id)
            ->orderBy("id", "DESC")
            ->first();

        $domain = Domain::select("name")
            ->where("project_id", $this->project_id)
            ->where("status", 3)
            ->first();
        $domainName = $domain->name ?? "nexuspay.vip";

        $boletoLink =
            "https://checkout.{$domainName}/order/" .
            Hashids::connection("sale_id")->encode($this->id) .
            "/download-boleto";

        $customer = [
            "name" => $this->customer->name ?? "",
            "document" => $this->customer->document ?? "",
            "email" => $this->customer->email ?? "",
            "telephone" => $this->customer->telephone ?? "",
        ];

        return [
            "id" => Hashids::connection("sale_id")->encode($this->id),
            "amount" => (float) number_format($amount, 2, ".", ""),
            "fee" => (float) number_format($fee, 2, ".", ""),
            "net_amount" => (float) number_format($netAmount, 2, ".", ""),
            "payment_method" => $this->present()->getPaymentType(),
            "status" => $this->present()->getStatus(),
            "created_at" => $this->start_date,
            "approved_at" => $this->end_date,
            "refunded_at" => $this->date_refunded,
            "products" => $this->products ?? [],
            "billet_digitable_line" => $this->boleto_digitable_line,
            "billet_url" => $boletoLink,
            "billet_due_date" => $this->boleto_due_date,
            "pix_code" => $pixCharge ? $pixCharge->qrcode : "",
            "pix_expires_at" => $this->boleto_link,
            "credit_card_installments" => $this->installments_amount,
            "customer" => $customer,
        ];
    }
}
