<?php

namespace Modules\Collaborators\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

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
            'id'        => Hashids::encode($this->id),
            'name'      => $this->name,
            'email'     => $this->email,
            'document'  => $this->document,
            'cellphone' => $this->cellphone,
            'date'      => $this->created_at->format('d/m/Y'),
            'role'      => $this->roles[0]->name,
        ];
    }
}
