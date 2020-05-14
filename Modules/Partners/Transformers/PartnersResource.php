<?php

namespace Modules\Partners\Transformers;

use League\Fractal\TransformerAbstract;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnersResource extends JsonResource
{
    protected $defaultIncludes = ['user'];

    public function toArray($request)
    {
        return [
            'partnersId' => Hashids::encode($this->id),
            'name'       => $this->userId->name ?? '',
            'type'       => $this->type == 'partner' ? 'parceiro' : 'produtor',
            'status'     => $this->status == 'active' ? 'ativo' : 'desativado',

        ];
    }
}
