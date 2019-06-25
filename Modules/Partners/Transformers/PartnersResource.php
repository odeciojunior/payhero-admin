<?php

namespace Modules\Partners\Transformers;

use App\Entities\User;
use League\Fractal\TransformerAbstract;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class PartnersResource extends Resource
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
