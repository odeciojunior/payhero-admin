<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Domain;
use Modules\Core\Services\CloudFlareService;
use Illuminate\Support\Facades\Log;

class UpdateDnsFromDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updatedns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {

            $domains = Domain::with([
                                        'domainsRecords' => function ($query) {
                                            $query->whereIn('name', ['checkout', 'affiliate', 'tracking']);
                                        }
                                    ])->orderByDesc('id')->get();

            $cloudFlareService = new CloudFlareService();
            $count = 0;
            foreach ($domains as $domain) {
                $this->line('Atualizando dominio :' . $domain->name);
                if (empty($domain->cloudflare_domain_id)) {
                    continue;
                }

                if ($count++ % 100 == 0) {
                    $cloudFlareService = new CloudFlareService();
                }

                $checkoutDns = $domain->domainsRecords->where('name', 'checkout')->first();
                $trackingDns = $domain->domainsRecords->where('name', 'tracking')->first();
                $affiliateDns = $domain->domainsRecords->where('name', 'affiliate')->first();

                if (!empty($checkoutDns)) {
                    $this->line('Atualizando dns :' . $checkoutDns->name);

                    $response = $cloudFlareService->updateRecordDetails(
                        $domain->cloudflare_domain_id,
                        $checkoutDns->cloudflare_record_id,
                        [
                            'type' => $checkoutDns->type,
                            'name' => $checkoutDns->name,
                            'content' => 'alb-production-1620949233.us-east-2.elb.amazonaws.com',
                            'proxied' => true
                        ]
                    );


                    if ($response) {
                        $checkoutDns->update(['content' => 'alb-production-1620949233.us-east-2.elb.amazonaws.com']);
                    }
                }
                if (!empty($trackingDns)) {
                    $this->line('Atualizando dns :' . $trackingDns->name);

                    $response = $cloudFlareService->updateRecordDetails(
                        $domain->cloudflare_domain_id,
                        $trackingDns->cloudflare_record_id,
                        [
                            'type' => $trackingDns->type,
                            'name' => $trackingDns->name,
                            'content' => 'alb-production-1620949233.us-east-2.elb.amazonaws.com',
                            'proxied' => true
                        ]
                    );

                    if ($response) {
                        $trackingDns->update(['content' => 'alb-production-1620949233.us-east-2.elb.amazonaws.com']);
                    }
                }
                if (!empty($affiliateDns)) {
                    $this->line('Atualizando dns :' . $affiliateDns->name);

                    $response = $cloudFlareService->updateRecordDetails(
                        $domain->cloudflare_domain_id,
                        $affiliateDns->cloudflare_record_id,
                        [
                            'type' => $affiliateDns->type,
                            'name' => $affiliateDns->name,
                            'content' => 'alb-production-1620949233.us-east-2.elb.amazonaws.com',
                            'proxied' => true
                        ]
                    );

                    if ($response) {
                        $affiliateDns->update(['content' => 'alb-production-1620949233.us-east-2.elb.amazonaws.com']);
                    }
                }
            }

        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }
}
