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
        $customer = $this->customer;
        $project = $this->project;
        $domain = $project->domains->first();

        $status = 'Recuperado';
        if ($this->payment_method == 1 || $this->payment_method == 3) {
            $status = 'Recusado';
            $type = 'cart_refundend';
        } else {
            $status = 'Expirado';
            $type = 'expired';
        }

        $link = 'Domínio não configurado';
        if (!empty($domain)) {
            $link = "https://checkout." . $domain->name . "/recovery/" . $this->id_log_session;
        }

        $emailStatus = 'Email inválido';
        if (FoxUtils::validateEmail($customer->email)) {
            $emailStatus = ($this->email_sent_amount == null) ? 'Não enviado' : $this->email_sent_amount;
        }

        return [
            'type' => $type,
            'id' => Hashids::connection('sale_id')->encode($this->id),
            'id_default' => Hashids::encode($this->id),
            'start_date' => with(new Carbon($this->created_at))->format('d/m/Y H:i:s'),
            'project' => $project->name,
            'client' => $customer->name,
            'email_status' => $emailStatus,
            'sms_status' => ($this->sms_sent_amount == null) ? 'Não enviado' : $this->sms_sent_amount,
            'recovery_status' => $status,
            'total_paid' => number_format($this->value, 2, ',', '.'),
            'link' => $link,
            'whatsapp_link' => "https://api.whatsapp.com/send?phone=" . preg_replace("/[^0-9]/", "", $customer->telephone) . '&text=Olá ' . $customer->present()
                    ->getFirstName(),
        ];
    }
}
