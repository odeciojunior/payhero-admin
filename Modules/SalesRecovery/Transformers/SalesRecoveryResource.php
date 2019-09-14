<?php

namespace Modules\SalesRecovery\Transformers;

use Carbon\Carbon;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Domain;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\CheckoutPlan;
use Illuminate\Http\Resources\Json\Resource;

class SalesRecoveryResource extends Resource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Exception
     */
    public function toArray($request)
    {
        return [
            'id'              => Hashids::encode($this->id),
            'date'            => with(new Carbon($this->created_at))->format('d/m/Y H:i:s'),
            'project'         => $this->project->name,
            'client'          => $this->name,
            'email_status'    => $this->present()->getEmailSentAmount(),
            'sms_status'      => $this->present()->getSmsSentAmount(),
            'recovery_status' => $this->status == 'abandoned cart' ? 'Não recuperado' : 'Recuperado',
            'value'           => number_format(intval(preg_replace("/[^0-9]/", "", $this->present()->getSubTotal($this->checkoutPlans))) / 100, 2, ',', '.'),
            'link'            => $this->present()->getCheckoutLink($this->project->domains->first()),
            'whatsapp_link'   => "https://api.whatsapp.com/send?phone=" . FoxUtils::prepareCellPhoneNumber($this->telephone) . '&text=Olá ' . explode(' ', $this->name)[0],
        ];
    }
}
