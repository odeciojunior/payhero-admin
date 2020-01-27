<?php

namespace Modules\Checkouts\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Core\Services\CheckoutService;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @property mixed project
 * @property mixed created_at
 * @property mixed id
 * @property mixed name
 * @property mixed status
 * @property mixed telephone
 * @property mixed checkoutPlans
 */
class CheckoutIndexResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request)
    {
        $checkoutService = app()->make(CheckoutService::class);

        return [
            'id'               => Hashids::encode($this->id),
            'date'             => with(new Carbon($this->created_at))->format('d/m/Y H:i:s'),
            'project'          => $this->project->name,
            'client'           => $this->client_name,
            'email_status'     => $this->present()->getEmailSentAmount(),
            'sms_status'       => $this->present()->getSmsSentAmount(),
            'status_translate' => $this->status == 'abandoned cart' ? 'Não recuperado' : 'Recuperado',
            'value'            => number_format(intval(preg_replace("/[^0-9]/", "", $checkoutService->getSubTotal($this->checkoutPlans))) / 100, 2, ',', '.'),
            'link'             => $this->present()->getCheckoutLink($this->project->domains->first()),
            'whatsapp_link'    => "https://api.whatsapp.com/send?phone=" . $this->client_telephone . '&text=Olá ' . explode(' ', $this->name)[0],
        ];
    }
}
