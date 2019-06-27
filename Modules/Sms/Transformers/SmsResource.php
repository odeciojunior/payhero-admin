<?php

namespace Modules\Sms\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Vinkla\Hashids\Facades\Hashids;

class SmsResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        //        $event = '';
        //        if ($this->event == 'boleto_generated') {
        //            $event = 'Boleto gerado';
        //        }

        return [
            'id'      => Hashids::encode($this->id),
            'event'   => $this->event,
            'time'    => $this->time,
            'period'  => $this->period,
            'message' => $this->message,
            'status'  => $this->status,
        ];
    }
}
