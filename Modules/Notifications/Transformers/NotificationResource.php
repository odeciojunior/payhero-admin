<?php

namespace Modules\Notifications\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class NotificationResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'read'    => $this->read_at != null ? 1 : 0,
            'type'    => explode("\\", $this->type)[count(explode("\\", $this->type)) -1],
            'date'    => date('d/m/Y H:m:s', strtotime($this->updated_at)),
            'message' => !empty($this->data['message']) ? $this->data['message'] : $this->data['qtd'],
        ];
    }
}
