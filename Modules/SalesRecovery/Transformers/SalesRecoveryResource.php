<?php

namespace Modules\SalesRecovery\Transformers;

use Carbon\Carbon;
use App\Entities\Log;
use App\Entities\Plan;
use App\Entities\Domain;
use App\Entities\CheckoutPlan;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;
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

        $status = '';
        if ($this->status == 'abandoned cart') {
            $status = 'Não recuperado';
        } else {
            $status = 'Recuperado'; 
        }

        $value         = 0;
        $plannCheckout = CheckoutPlan::where('checkout', $this->id)->get()->toArray();
        foreach ($plannCheckout as $planCheckout) {
            $plan  = Plan::find($planCheckout['plan']);
            $value += str_replace('.', '', $plan['price']) * $planCheckout['amount'];
        }

        $domain = Domain::where('project_id', $this->project)->first();
        $link   = "https://checkout." . $domain['name'] . "/recovery/" . $this->id_log_session;

        if ($this->email_sent_amount == null || $this->email_sent_amount == 0) {
            $emailSentAmount = 'Não enviado';
        } else {
            $emailSentAmount = $this->email_sent_amount;
        }

        if ($this->sms_sent_amount == null || $this->sms_sent_amount == 0) {
            $smsSentAmount = 'Não enviado';
        } else {
            $smsSentAmount = $this->sms_sent_amount;
        }

        return [
            'id'              => Hashids::encode($this->id),
            'date'            => with(new Carbon($this->created_at))->format('d/m/Y H:i:s'),
            'project'         => $this->projectModel->name,
            'client'          => $this->name,
            'email_status'    => $emailSentAmount,
            'sms_status'      => $smsSentAmount,
            'recovery_status' => $status,
            'value'           => number_format(intval(preg_replace("/[^0-9]/", "", $value)) / 100, 2, ',', '.'),
            'link'            => $link,
            'whatsapp_link'   => "https://api.whatsapp.com/send?phone=" . FoxUtils::prepareCellPhoneNumber($this->telephone) . '&text=Olá ' . explode(' ', $this->name)[0],
        ];
    }

}
