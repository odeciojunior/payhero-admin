<?php

namespace Modules\CartRecovery\Transformers;

use App\Entities\Project;
use Carbon\Carbon;
use App\Entities\Log;
use App\Entities\Plan;
use App\Entities\Domain;
use App\Entities\CheckoutPlan;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class CartRecoveryResource extends Resource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Exception
     */
    public function toArray($request)
    {
        $client = '';
        $log    = Log::where('id_log_session', $this->id_log_session)->orderBy('id', 'DESC')->first();

        if ($log) {
            $client = $log->name;
        }

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
        $value = substr_replace($value, '.', strlen($value) - 2, 0);

        $domain = Domain::where('project_id', $this->project)->first();
        $link   = "https://checkout." . $domain['name'] . "/recovery/" . $this->id_log_session;

        $whatsAppMsg = 'Olá '.$log->name;

        return [
            'id'              => Hashids::encode($this->id),
            'date'            => with(new Carbon($this->created_at))->format('d/m/Y H:i:s'),
            'project'         => $this->projectModel->name,
            'client'          => $client,
            'email_status'    => 'Não enviado',
            'sms_status'      => 'Não enviado',
            'recovery_status' => $status,
            'value'           => $value,
            'link'            => $link,
            'whatsapp_link'   => "https://api.whatsapp.com/send?phone=55" . preg_replace('/[^0-9]/', '', $log->telephone).'&text='.$whatsAppMsg,
        ];

        return parent::toArray($request);
    }
}
