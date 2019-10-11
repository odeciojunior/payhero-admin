<?php

namespace Modules\Dashboard\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Companies\Transformers\CompanyResource;
use Modules\Core\Entities\User;

/**
 * Class DashboardResumeCollection
 * @package Modules\Dashboard\Transformers
 */
class DashboardResumeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        /** @var User $user */
        $user = auth()->user();
        $user->loadMissing('companies');

        return [
            'data'      => DashboardResumeResource::collection($this->collection),
            'companies' => CompanyResource::collection($user->companies),
            //            'links'  => [
            //                'self' => 'link-value',
            //            ],
        ];
    }
}
