<?php

namespace Modules\Products\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CreateProductResource extends JsonResource
{
    public function toArray($request)
    {
        $categories = [];
        foreach ($this->resource['categories'] as $category) {
            $categories[] = [
                'id'   => $category->id_code,
                'name' => $category->name,
            ];
        }

        return [
            'categories' => $categories,
        ];
    }
}
