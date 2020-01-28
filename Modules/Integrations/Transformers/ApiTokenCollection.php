<?php

namespace Modules\Integrations\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class ApiTokenCollection
 * @package Modules\Integrations\Transformers
 */
class ApiTokenCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $tokens  = ApiTokenResource::collection($this->collection)->toArray($request);
        $actives = array_filter(
            $tokens,
            function($item) {
                return $item['status'] == 'active';
            }
        );

        return [
            'data'   => $tokens,
            'resume' => $this->when(
                (bool) $request->get('resume', false),
                [
                    'total'    => count($tokens),
                    'active'   => count($actives),
                    'received' => 0,
                    'sent'     => 0,
                ]
            ),
            'links'  => [
                'self' => 'link-value',
            ],
        ];
    }
}
