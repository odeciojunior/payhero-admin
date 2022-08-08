<?php

namespace Modules\Checkouts\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Services\CheckoutService;
use Modules\Core\Services\FoxUtils;

class CheckoutIndexResource extends JsonResource
{
    public function toArray($request): array
    {
        $value = number_format(
            intval(FoxUtils::onlyNumbers((new CheckoutService())->getSubTotal($this->checkoutPlans))) / 100,
            2,
            ",",
            "."
        );
        $wppMessage =
            "https://api.whatsapp.com/send?phone=+55" .
            FoxUtils::onlyNumbers($this->client_telephone) .
            "&text=Olá " .
            explode(" ", $this->name)[0];

        return [
            "id" => hashids_encode($this->id),
            "date" => $this->created_at->format("d/m/Y H:i:s"),
            "project" => $this->project->name,
            "client" => $this->client_name ?? "",
            "email_status" => $this->present()->getEmailSentAmount(),
            "sms_status" => $this->present()->getSmsSentAmount(),
            "status_translate" => $this->status == "abandoned cart" ? "Não recuperado" : "Recuperado",
            "value" => $value,
            "link" => $this->present()->getCheckoutLink($this->project->domains->first()),
            "whatsapp_link" => $wppMessage,
        ];
    }
}
