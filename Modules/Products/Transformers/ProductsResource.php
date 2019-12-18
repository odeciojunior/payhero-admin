<?php

namespace Modules\Products\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

/**
 * Class ProductsResource
 * @package Modules\Products\Transformers
 */
class ProductsResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id_code,
            'id_view'     => ($this->shopify == 1 ? $this->shopify_id : $this->id_code),
            'name'        => mb_substr($this->name, 0, 18),
            'description' => mb_substr($this->description, 0, 20),
            'image'       => $this->photo == '' ? 'modules/global/img/semimagem.png' : $this->photo,
            'link'        => '/api/products/' . $this->id_code . '/edit',
            'created_at'  => Carbon::parse($this->created_at)->format('d/m/Y'),

        ];
    }
}
