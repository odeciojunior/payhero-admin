<?php

namespace Modules\SalesRecovery\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\FoxUtils;
use Monolog\Handler\SlackHandler;
use Vinkla\Hashids\Facades\Hashids;

class SalesRecoveryCardRefusedResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request)
    {
        $customer = $this->customer;
        $project = $this->project;
        $domain = $project->domains->where("status", (new Domain())->present()->getStatus("approved"))->first();

        if ($this->payment_method == 1 || $this->payment_method == 3) {
            $status = "Recusado";
            $type = "cart_refundend";
        } else {
            $status = "Expirado";
            $type = "expired";
        }

        if ($this->payment_method === Sale::PIX_PAYMENT) {
            if (FoxUtils::isProduction()) {
                $link = !empty($domain)
                    ? "https://checkout." . $domain->name . "/pix/" . Hashids::connection("sale_id")->encode($this->id)
                    : "Domínio não configurado";
            } else {
                $link =
                    env("CHECKOUT_URL", "http://dev.checkout.com.br") .
                    "/pix/" .
                    Hashids::connection("sale_id")->encode($this->id);
            }
        } else {
            if (FoxUtils::isProduction()) {
                $link = !empty($domain)
                    ? "https://checkout." . $domain->name . "/recovery/" . Hashids::encode($this->checkout_id)
                    : "Domínio não configurado";
            } else {
                $link =
                    env("CHECKOUT_URL", "http://dev.checkout.com.br") .
                    "/recovery/" .
                    Hashids::encode($this->checkout_id);
            }
        }

        $emailStatus = "Email inválido";
        if (FoxUtils::validateEmail($customer->email)) {
            $emailStatus = $this->email_sent_amount == null ? "Não enviado" : $this->email_sent_amount;
        }

        return [
            "type" => $type,
            "id" => Hashids::connection("sale_id")->encode($this->id),
            "id_default" => Hashids::encode($this->id),
            "start_date" => with(new Carbon($this->created_at))->format("d/m/Y H:i:s"),
            "project" => $project->name,
            "client" => $customer->name,
            "email_status" => $emailStatus,
            "sms_status" => $this->sms_sent_amount == null ? "Não enviado" : $this->sms_sent_amount,
            "recovery_status" => $status,
            "total_paid" => number_format($this->value, 2, ",", "."),
            "link" => $link,
            "whatsapp_link" =>
                "https://api.whatsapp.com/send?phone=" .
                preg_replace("/[^0-9]/", "", $customer->telephone) .
                "&text=Olá " .
                $customer->present()->getFirstName(),
        ];
    }
}
