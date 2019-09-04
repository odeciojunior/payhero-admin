<?php

namespace Modules\Products\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class ProductsResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id_code,
            'name'       => substr($this->name, 0, 18),
            'image'      => $this->photo == '' ? 'modules/global/img/semimagem.png' : $this->photo,
            'link'       => '/api/products/' . $this->id_code . '/edit',
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),

        ];
    }
}
