<?php

namespace Modules\Dominios\Transformers;

use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Endpoints\Zones;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class DomainResource extends Resource
{
    /**
     * @var CloudFlareService
     */
    private $cloudFlareService;
    /**
     * @var SendgridService
     */
    private $sendgridService;

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
     * @return \Illuminate\Contracts\Foundation\Application|mixed|SendgridService
     */
    private function getSendgridService()
    {
        if (!$this->sendgridService) {
            $this->sendgridService = app(SendgridService::class);
        }

        return $this->sendgridService;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Cloudflare\API\Endpoints\EndpointException
     */
    public function toArray($request)
    {

        if ($this->status != $this->getEnum('status', 'approved')) {

            $activated = null;
            $responseValidateDomain = null;
            $responseValidateLink = null;
/*
            $activated = $this->getCloudFlareService()->activationCheck($this->name);

            $linkBrandResponse = $this->getSendgridService()->getLinkBrand($this->name);
            $sendgridResponse  = $this->getSendgridService()->getZone($this->name);

            if (!empty($linkBrandResponse) && !empty($sendgridResponse)) {
                $responseValidateDomain = $this->getSendgridService()->validateDomain($sendgridResponse->id);
                $responseValidateLink   = $this->getSendgridService()->validateBrandLink($linkBrandResponse->id);
            }

            if ($activated && $responseValidateDomain && $responseValidateLink) {
                $this->update([
                                  'status' => $this->resource->getEnum('status', 'approved'),
                              ]);
            }
*/
            if ($this->getCloudFlareService()->checkHtmlMetadata('https://checkout.' . $this->name, 'checkout-cloudfox', '1')) {
                $this->update([
                                  'status' => $this->resource->getEnum('status', 'approved'),
                              ]);
            }
        }

        return [
            'id'                => Hashids::encode($this->id),
            'domain'            => $this->name,
            'ip_domain'         => ($this->project->shopify_id == null) ? $this->domain_ip : 'Shopify',
            'status'            => $this->status,
            'status_translated' => $this->getEnum('status', $this->status, true),
        ];
    }
}
