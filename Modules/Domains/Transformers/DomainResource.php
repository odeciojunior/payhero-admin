<?php

namespace Modules\Domains\Transformers;

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
    public function toArray($request)
    {

        return [
            'id'                => Hashids::encode($this->id),
            'domain'            => $this->name,
            'ip_domain'         => ($this->project->shopify_id == null) ? $this->domain_ip : 'Shopify',
            'status'            => $this->status,
            'status_translated' => $this->getEnum('status', $this->status, true),
        ];
    }
}
