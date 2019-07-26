<?php

namespace Modules\SalesRecovery\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;

class SalesRecoveryCardRefusedResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request)
    {
        $client  = $this->getRelation('clientModel');
        $project = $this->getRelation('projectModel');
        $domain  = $project->getRelation('domains')->first();

        $link = 'Dominio não configurado';
        if (!empty($domain)) {
            $link = "https://checkout." . $domain->name . "/recovery/" . $this->id_log_session;
        }

        $emailStatus = 'Email inválido';
        if (FoxUtils::validateEmail($client->email)) {
            $emailStatus = ($this->email_sent_amount == null) ? 'Não enviado' : $this->email_sent_amount;
        }

        $whatsAppMsg       = 'Olá ' . $client->name;
        $client->telephone = FoxUtils::prepareCellPhoneNumber($client->telephone);

        return [
            'id'              => Hashids::encode($this->id),
            'date'            => with(new Carbon($this->created_at))->format('d/m/Y H:i:s'),
            'project'         => $project->name,
            'client'          => $client->name,
            'email_status'    => $emailStatus,
            'sms_status'      => ($this->sms_sent_amount == null) ? 'Não enviado' : $this->sms_sent_amount,
            'recovery_status' => 'Recusado',
            'value'           => $this->value,
            'link'            => $link,
            'whatsapp_link'   => "https://api.whatsapp.com/send?phone=55" . $client->telephone . '&text=' . $whatsAppMsg,
        ];
    }
}
