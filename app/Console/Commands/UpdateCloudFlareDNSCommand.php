<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\DomainRecord;
use Illuminate\Support\Facades\Log;
use Exception;

class UpdateCloudFlareDNSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudflare:update-dns 
                            {--dry-run : Execute without making actual changes}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update CloudFlare DNS records and synchronize database records with new IPs';

    /**
     * @var CloudFlareService
     */
    protected $cloudFlareService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CloudFlareService $cloudFlareService)
    {
        parent::__construct();
        $this->cloudFlareService = $cloudFlareService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $isForce = $this->option('force');
        
        $this->info('Starting CloudFlare DNS update and sync process...');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No actual changes will be made');
        }

        // Define update mapping
        $updateMap = [
            'checkout' => CloudFlareService::checkoutIp,
            'sac' => CloudFlareService::sacIp,
            'affiliate' => CloudFlareService::affiliateIp,
            'tracking' => CloudFlareService::adminIp
        ];

        // Show current IPs when in verbose mode
        if ($this->getOutput()->isVerbose()) {
            $this->info("\nTarget IP configurations:");
            $this->table(
                ['Service', 'Target IP/Domain'],
                array_map(function($key, $value) {
                    return [$key, $value];
                }, array_keys($updateMap), $updateMap)
            );
        }

        // Confirmation prompt if not in force mode
        if (!$isDryRun && !$isForce) {
            if (!$this->confirm('This will update DNS records for all domains. Continue?')) {
                $this->info('Operation cancelled by user.');
                return 0;
            }
        }

        $this->output->newLine();
        $this->line('Fetching domains from database...');

        try {
            // Get all active domains
            $domains = Domain::whereNull('deleted_at')->get();
            
            $totalDomains = $domains->count();
            $this->info("Found {$totalDomains} active domains");

            // Summary counters
            $totalUpdated = 0;
            $totalErrors = 0;
            $totalSkipped = 0;

            foreach ($domains as $domain) {
                if ($this->getOutput()->isVerbose()) {
                    $this->info("\nProcessing domain: " . $domain->name);
                }

                // Get records that need updating
                $records = DomainRecord::where('domain_id', $domain->id)
                    ->where('type', 'CNAME')
                    ->whereIn('name', array_keys($updateMap))
                    ->get();

                if ($records->isEmpty()) {
                    $totalSkipped++;
                    if ($this->getOutput()->isVerbose()) {
                        $this->line('  • No matching records found');
                    }
                    continue;
                }

                foreach ($records as $record) {
                    $newContent = $updateMap[$record->name];
                    
                    if ($record->content === $newContent) {
                        if ($this->getOutput()->isVerbose()) {
                            $this->line("  • {$record->name} already up to date");
                        }
                        continue;
                    }

                    if ($this->getOutput()->isVerbose()) {
                        $this->line("  • Updating {$record->name}:");
                        $this->line("    From: {$record->content}");
                        $this->line("    To:   {$newContent}");
                    }

                    if (!$isDryRun) {
                        try {
                            // Update in Cloudflare
                            $updateSuccess = $this->cloudFlareService->updateRecordDetails(
                                $domain->cloudflare_domain_id,
                                $record->cloudflare_record_id,
                                [
                                    'type' => 'CNAME',
                                    'name' => $record->name,
                                    'content' => $newContent,
                                    'proxied' => (bool)$record->proxy
                                ]
                            );

                            if ($updateSuccess) {
                                // Update in database
                                $record->update([
                                    'content' => $newContent
                                ]);

                                $totalUpdated++;
                                if ($this->getOutput()->isVerbose()) {
                                    $this->info("    ✓ Updated successfully");
                                }
                            } else {
                                $totalErrors++;
                                $this->error("    ✗ Failed to update in Cloudflare");
                            }
                        } catch (Exception $e) {
                            $totalErrors++;
                            $this->error("    ✗ Error: " . $e->getMessage());
                            Log::channel('cloudflare')->error('Error updating record', [
                                'domain' => $domain->name,
                                'record' => $record->name,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }

            // Display summary
            $this->output->newLine();
            $this->info('=== Update Summary ===');
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total Domains', $totalDomains],
                    ['Records Updated', $totalUpdated],
                    ['Domains Skipped', $totalSkipped],
                    ['Errors', $totalErrors],
                ]
            );

            if ($isDryRun) {
                $this->warn('This was a dry run. No actual changes were made.');
            } else {
                $this->info('DNS records have been updated successfully.');
            }

            Log::channel('cloudflare')->info('DNS update command completed', [
                'total_domains' => $totalDomains,
                'total_updated' => $totalUpdated,
                'total_skipped' => $totalSkipped,
                'total_errors' => $totalErrors
            ]);

            return 0;
        } catch (Exception $e) {
            $this->error('An unexpected error occurred: ' . $e->getMessage());
            
            if ($this->getOutput()->isVerbose()) {
                $this->error('Stack trace:');
                $this->error($e->getTraceAsString());
            }

            Log::channel('cloudflare')->error('Unexpected error in DNS update command', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }
}