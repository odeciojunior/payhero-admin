<?php

namespace Modules\ProjectNotification\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

/**
 * Class ProjectNotificationResource
 * @package Modules\ProjectNotification\Transformers
 */
class ProjectNotificationResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $arrayType = [1 => 'Email', 2 => 'SMS'];
        $arrayEvent = [
			1 => 'Boleto gerado',
			2 => 'Boleto compensado',
			3 => 'Compra no cartão',
			4 => 'Carrinho abandonado',
			5 => 'Boleto vencendo',
			6 => 'Código de Rastreio'
		];

        $arrayMessage = json_decode($this->message, true);
        $subject = $arrayMessage['subject'] ?? '';
        $title  = $arrayMessage['title'] ?? '';
        $message = (is_array($arrayMessage)) ? ($arrayMessage['content'] ?? '') : $this->message;

        return [
            'id'                => $this->id_code,
            'status' 	        => $this->status,
            'status_translated' => ($this->status == 1) ? 'Ativo' : 'Inativo',
            'notification_enum' => $this->notification_enum,
            'type_enum'         => $this->type_enum,
            'event_enum'        => $this->event_enum,
            'time' 		        => $this->time,
            'message'           => $message,
            'subject'           => $subject,
            'title'             => $title,
            'type'   	        => $arrayType[$this->type_enum],
            'event' 	        => $arrayEvent[$this->event_enum],
            'project_name'      => $this->project_name ?? '',
            'project_contact'   => $this->project_contact ?? '',
            'project_image'     => $this->project_image ?? '',
        ];
    }
}
