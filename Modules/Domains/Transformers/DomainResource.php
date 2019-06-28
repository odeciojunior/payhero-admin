<?php

namespace Modules\Dominios\Transformers;

use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Endpoints\Zones;
use Modules\Core\Services\CloudFlareService;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class DomainResource extends Resource
{
    /**
     * @var CloudFlareService
     */
    private $cloudFlareService;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed|CloudFlareService
     */
    private function getCloudFlareService()
    {
        if (!$this->cloudFlareService) {
            $this->cloudFlareService = app(CloudFlareService::class);
        }

        return $this->cloudFlareService;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Cloudflare\API\Endpoints\EndpointException
     */
    public function toArray($request)
    {

        if ($this->status != $this->getEnum('status', 'approved')) {
            $activated = $this->getCloudFlareService()->activationCheck($this->name);

            if ($activated) {
                $this->update([
                                            'status' => $this->resource->getEnum('status', 'approved'),
                                        ]);
            }
        }

        return [
            'id'                => Hashids::encode($this->id),
            'domain'            => $this->name,
            'ip_domain'         => $this->domain_ip,
            'status'            => $this->status,
            'status_translated' => $this->getEnum('status', $this->status, true),
        ];
    }
}
