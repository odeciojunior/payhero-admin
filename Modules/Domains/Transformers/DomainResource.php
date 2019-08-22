<?php

namespace Modules\Domains\Transformers;

use Illuminate\Support\Facades\Lang;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class DomainResource extends Resource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Cloudflare\API\Endpoints\EndpointException
     */
    public function toArray($request) {

        return [
            'id'                => Hashids::encode($this->id),
            'domain'            => $this->name,
            'status'            => $this->status,
            'status_translated' => Lang::get('definitions.enum.status.' . $this->getEnum('status', $this->status)),
        ];
    }

}
