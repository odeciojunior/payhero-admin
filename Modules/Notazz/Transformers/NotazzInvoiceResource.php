<?php

namespace Modules\Notazz\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use Vinkla\Hashids\Facades\Hashids;

class NotazzInvoiceResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'               => Hashids::encode($this->id),
            'invoice_type'     => $this->invoice_type,
            'date_pending'     => ($this->date_pending) ? Carbon::parse($this->date_pending)
                                                                ->format('d/m/Y H:i:s') : null,
            'date_sent'        => ($this->date_sent) ? Carbon::parse($this->date_sent)
                                                             ->format('d/m/Y H:i:s') : null,
            'date_completed'   => ($this->date_completed) ? Carbon::parse($this->date_completed)
                                                                  ->format('d/m/Y H:i:s') : null,
            'date_error'       => ($this->date_error) ? Carbon::parse($this->date_error)
                                                              ->format('d/m/Y H:i:s') : null,
            'date_rejected'    => ($this->date_rejected) ? Carbon::parse($this->date_rejected)
                                                                 ->format('d/m/Y H:i:s') : null,
            'date_canceled'    => ($this->date_canceled) ? Carbon::parse($this->date_canceled)
                                                                 ->format('d/m/Y H:i:s') : null,
            'schedule'         => ($this->schedule) ? Carbon::parse($this->schedule)
                                                            ->format('d/m/Y H:i:s') : null,
            'status'           => $this->status,
            'xml'              => $this->xml,
            'pdf'              => $this->pdf,
            'return_http_code' => $this->return_http_code,
            'return_message'   => $this->return_message,
            'return_status'    => $this->return_status,
        ];
    }
}
