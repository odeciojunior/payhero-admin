<?php

namespace Modules\Notifications\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Services\FoxUtils;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $time = "";
        $time = FoxUtils::calcTime($this->created_at);

        return [
            "read" => $this->read_at != null ? 1 : 0,
            "type" => explode("\\", $this->type)[count(explode("\\", $this->type)) - 1],
            //'date'    => date('d/m/Y H:m:s', strtotime($this->created_at)),
            "time" => $time,
            "message" => !empty($this->data["message"]) ? $this->data["message"] : $this->data["qtd"],
        ];
    }
}
