<?php

namespace Modules\Domains\Transformers;

use Modules\Core\Services\CloudFlareService;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class DomainRecordsIndexResource extends Resource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Cloudflare\API\Endpoints\EndpointException]
     */
    public function toArray($request)
    {
        $cloudFlareService = new CloudFlareService();
        $haveEnterA        = false;
        $registers         = [];

        foreach ($this->resource['domainRecords'] as $record) {

            if ($record->type == 'A' && $record->name == $this->resource['domain']->name) {
                $haveEnterA = true;
            }
            $subdomain = explode('.', $record->name);

            switch ($record->content) {
                case $cloudFlareService::shopifyIp:
                    //                    $content = $record->content;
                    $content = "Servidores Shopify";
                    break;
                case $cloudFlareService::checkoutIp:
                case $cloudFlareService::adminIp:
                case $cloudFlareService::sacIp:
                case $cloudFlareService::affiliateIp:
                    $content = "Servidores CloudFox";
                    break;
                default:
                    $content = $record->content;
                    break;
            }

            $newRegister = [
                'id'          => Hashids::encode($record->id),
                'type'        => $record->type,
                'proxy'       => $record->proxy,
                'domain_name' => $this->resource['domain']->name,
                //'name'        => ($record->name == $domain['name']) ? $record->name : ($subdomain[0] ?? ''),
                'name'        => $record->name,
                'content'     => substr($content, 0, 20),
                'system_flag' => $record->system_flag,

            ];

            $registers[] = $newRegister;
        }

        return [
            'domainRecords' => $registers,
        ];
    }
}
