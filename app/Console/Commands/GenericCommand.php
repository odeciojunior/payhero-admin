<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\DomainRecord;
use Modules\Core\Services\CloudFlareService;

class GenericCommand extends Command
{
    protected $signature = 'generic {user?}';

    protected $description = 'Command description';

    private $cloudflareService;

    public function __construct()
    {
        parent::__construct();

        $this->cloudflareService = new CloudFlareService();
    }

    public function handle()
    {
        $domains = Domain::with('project')->whereDate('created_at', '>=', '2021-04-20')->get();
        foreach ($domains as $domain) {
            $project = $domain->project;

            if (is_null($project->shopify_id)) {
                $this->integrationWebsite($domain);
            } else {
                $this->integrationShopify($domain);
            }
        }
    }

    private function integrationWebsite($domain)
    {
        $newZone = $this->cloudflareService->getZones($domain->name);

        if (empty($newZone)) {
            return true;
        }
        $newZone = $newZone[0];
        $this->cloudflareService->setZone($newZone->name);

        $this->cloudflareService->getSendgridService()->deleteZone($newZone->name);
        $sendgridResponse = $this->cloudflareService->getSendgridService()->addZone($newZone->name);

        foreach ($sendgridResponse->dns as $responseDns) {
            if ($responseDns->type == "mx") {
                $domainRecord = DomainRecord::where('domain_id', $domain->id)
                    ->where('type', 'MX')
                    ->where('name', $responseDns->host)
                    ->where('content', $responseDns->data)
                    ->where('system_flag', 1)
                    ->first();
                if (empty($domainRecord)) {
                    $recordId = $this->cloudflareService->addRecord(
                        'MX',
                        $responseDns->host,
                        $responseDns->data,
                        0,
                        false,
                        '1'
                    );

                    DomainRecord::create(
                        [
                            'domain_id' => $domain->id,
                            'cloudflare_record_id' => $recordId,
                            'type' => 'MX',
                            'name' => $responseDns->host,
                            'content' => $responseDns->data,
                            'system_flag' => 1,
                        ]
                    );
                }
            } else {
                $domainRecord = DomainRecord::where('domain_id', $domain->id)
                    ->where('type', strtoupper($responseDns->type))
                    ->where('name', $responseDns->host)
                    ->where('content', $responseDns->data)
                    ->where('system_flag', 1)
                    ->first();

                if (empty($domainRecord)) {
                    $recordId = $this->cloudflareService->addRecord(
                        strtoupper($responseDns->type),
                        $responseDns->host,
                        $responseDns->data,
                        0,
                        false
                    );

                    DomainRecord::create(
                        [
                            'domain_id' => $domain->id,
                            'cloudflare_record_id' => $recordId,
                            'type' => strtoupper($responseDns->type),
                            'name' => $responseDns->host,
                            'content' => $responseDns->data,
                            'system_flag' => 1,
                        ]
                    );
                }
            }
        }

        $this->cloudflareService->getSendgridService()->deleteLinkBrand($newZone->name);
        $linkBrandResponse = $this->cloudflareService->getSendgridService()->createLinkBrand($newZone->name);

        foreach ($linkBrandResponse->dns as $responseDns) {
            $linkBrand = DomainRecord::where('domain_id', $domain->id)
                ->where('type', strtoupper($responseDns->type))
                ->where('name', $responseDns->host)
                ->where('content', $responseDns->data)
                ->where('system_flag', 1)
                ->first();

            if (empty($linkBrand)) {
                $recordId = $this->cloudflareService->addRecord(
                    strtoupper($responseDns->type),
                    $responseDns->host,
                    $responseDns->data,
                    0,
                    false
                );
                DomainRecord::create(
                    [
                        'domain_id' => $domain->id,
                        'cloudflare_record_id' => $recordId,
                        'type' => strtoupper($responseDns->type),
                        'name' => $responseDns->host,
                        'content' => $responseDns->data,
                        'system_flag' => 1,
                    ]
                );
            }
        }

        return true;
    }

    private function integrationShopify($domain)
    {
        $newZone = $this->cloudflareService->getZones($domain->name);
        if (empty($newZone)) {
            return false;
        }

        $newZone = $newZone[0];
        $this->cloudflareService->setZone($newZone->name);

        $this->cloudflareService->getSendgridService()->deleteZone($newZone->name);
        $sendgridResponse = $this->cloudflareService->getSendgridService()->addZone($newZone->name);

        foreach ($sendgridResponse->dns as $responseDns) {
            if ($responseDns->type == 'mx') {
                $domainRecord = DomainRecord::where('type', 'mx')
                    ->where('domain_id', $domain->id)
                    ->where('name', $responseDns->host)
                    ->where('content', $responseDns->data)
                    ->where('system_flag', 1)->first();

                if (empty($domainRecord)) {
                    $recordId = $this->cloudflareService->addRecord(
                        'MX',
                        $responseDns->host,
                        $responseDns->data,
                        0,
                        false,
                        '1'
                    );

                    DomainRecord::create(
                        [
                            'domain_id' => $domain->id,
                            'cloudflare_record_id' => $recordId,
                            'type' => 'MX',
                            'name' => $responseDns->host,
                            'content' => $responseDns->data,
                            'system_flag' => 1,
                        ]
                    );
                }
            } else {
                $domainRecord = DomainRecord::where('domain_id', $domain->id)
                    ->where('type', strtoupper($responseDns->type))
                    ->where('name', $responseDns->host)
                    ->where('content', $responseDns->data)
                    ->where('system_flag', 1)
                    ->first();

                if (empty($domainRecord)) {
                    $recordId = $this->cloudflareService->addRecord(
                        strtoupper($responseDns->type),
                        $responseDns->host,
                        $responseDns->data,
                        0,
                        false
                    );

                    DomainRecord::create(
                        [
                            'domain_id' => $domain->id,
                            'cloudflare_record_id' => $recordId,
                            'type' => strtoupper($responseDns->type),
                            'name' => $responseDns->host,
                            'content' => $responseDns->data,
                            'system_flag' => 1,
                        ]
                    );
                }
            }
        }

        $this->cloudflareService->getSendgridService()->deleteLinkBrand($newZone->name);
        $linkBrandResponse = $this->cloudflareService->getSendgridService()->createLinkBrand($newZone->name);

        if (!empty($linkBrandResponse)) {
            foreach ($linkBrandResponse->dns as $responseDns) {
                $domainRecordLink = DomainRecord::where('domain_id', $domain->id)
                    ->where('type', strtoupper($responseDns->type))
                    ->where('name', $responseDns->host)
                    ->where('content', $responseDns->data)
                    ->where('system_flag', 1)
                    ->first();

                if (empty($domainRecordLink)) {
                    $recordId = $this->cloudflareService->addRecord(
                        strtoupper($responseDns->type),
                        $responseDns->host,
                        $responseDns->data,
                        0,
                        false
                    );

                    DomainRecord::create(
                        [
                            'domain_id' => $domain->id,
                            'cloudflare_record_id' => $recordId,
                            'type' => strtoupper($responseDns->type),
                            'name' => $responseDns->host,
                            'content' => $responseDns->data,
                            'system_flag' => 1,
                        ]
                    );
                }
            }
        }
        return true;
    }
}



