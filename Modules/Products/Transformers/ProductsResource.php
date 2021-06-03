<?php

namespace Modules\Products\Transformers;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProductsResource
 * @package Modules\Products\Transformers
 */
class ProductsResource extends JsonResource
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
            'name'        => Str::limit($this->name, 18),
            'description' => Str::limit($this->description, 25),
            'image'       => $this->photo == '' ? 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/product-default.png' : $this->photo,
            'link'        => '/api/products/' . $this->id_code . '/edit',
            'created_at'  => Carbon::parse($this->created_at)->format('d/m/Y'),
            'type_enum'   => $this->type_enum,
            'status_enum' => $this->status_enum,

        ];
    }
}
