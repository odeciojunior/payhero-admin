<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Domain;
use Modules\Core\Services\CloudFlareService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $this->info('ATUALIZANDO DNS RECORDS DO SAC');
        $this->line('Obtendo domínios...');

        $domains = Domain::with([
            'domainsRecords' => function ($query) {
                $query->where('name', 'sac');
            }
        ])->get();

        $this->line('Atualizando domínios...');

        $bar = $this->output->createProgressBar($domains->count());
        $bar->start();

        foreach ($domains as $domain) {
            try {
                if (empty($domain->cloudflare_domain_id)) {
                    continue;
                }
                $cloudFlareService = new CloudFlareService();

                $record = $domain->domainsRecords->first();
                if (!empty($record)) {
                    $response = $cloudFlareService->updateRecordDetails(
                        $domain->cloudflare_domain_id,
                        $record->cloudflare_record_id,
                        [
                            'type' => $record->type,
                            'name' => $record->name,
                            'content' => 'alb-production-1620949233.us-east-2.elb.amazonaws.com',
                            'proxied' => true
                        ]
                    );

                    if ($response) {
                        $record->update(['content' => 'alb-production-1620949233.us-east-2.elb.amazonaws.com']);
                    }
                }
            } catch (\Exception $e) {
                $this->error("\n    ERROR: " . $e->getMessage());
            }
            $bar->advance();
        }
        $bar->finish();
    }
}
