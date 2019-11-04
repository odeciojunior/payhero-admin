<?php

namespace Modules\Collaborators\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ClientResource
 * @property mixed name
 * @property mixed document
 * @property mixed email
 * @property mixed telephone
 * @property mixed id_code
 * @package Modules\Clients\Transformers
 */
class CollaboratorsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name'  => $this->name,
            'email' => $this->email,
            'date'  => $this->created_at,
        ];
    }
}
