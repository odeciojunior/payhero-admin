<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\DomainService;
use Modules\Core\Entities\Domain;
use Illuminate\Support\Facades\Log;
use Exception;
use Cloudflare\API\Endpoints\EndpointException;
use Carbon\Carbon;

class CleanupDomainsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:cleanup 
                            {--dry-run : Execute without making actual changes}
                            {--force : Skip confirmation prompt}
                            {--days=14 : Minimum age of domains to check in days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check domains older than 2 weeks in Cloudflare and remove inaccessible ones from the system';

    /**
     * @var CloudFlareService
     */
    protected $cloudFlareService;

    /**
     * @var DomainService
     */
    protected $domainService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CloudFlareService $cloudFlareService, DomainService $domainService)
    {
        parent::__construct();
        $this->cloudFlareService = $cloudFlareService;
        $this->domainService = $domainService;
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
        $minAgeDays = $this->option('days');
        
        $this->info('Starting domain cleanup process...');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No actual changes will be made');
        }

        // Calculate cutoff date
        $cutoffDate = Carbon::now()->subDays($minAgeDays);
        
        // Get domains older than cutoff date
        $domains = Domain::whereNull('deleted_at')
                        ->where('created_at', '<', $cutoffDate)
                        ->whereNotIn('name', ['pag.net.br','azcend.com.br'])
                        ->get();

        $totalDomains = $domains->count();
        $allDomainsCount = Domain::whereNull('deleted_at')->count();

        $this->info(sprintf(
            "Found %d domains older than %d days (out of %d total domains)",
            $totalDomains,
            $minAgeDays,
            $allDomainsCount
        ));

        if ($this->getOutput()->isVerbose()) {
            $this->line("Cutoff date: " . $cutoffDate->format('Y-m-d H:i:s'));
        }

        if (!$isDryRun && !$isForce) {
            if (!$this->confirm('This will check old domains and remove inaccessible ones. Continue?')) {
                $this->info('Operation cancelled by user.');
                return 0;
            }
        }

        // Get all zones from Cloudflare for comparison
        try {
            $cloudflareZones = collect($this->cloudFlareService->getZones());
            $this->info("Retrieved " . $cloudflareZones->count() . " zones from Cloudflare");
        } catch (Exception $e) {
            $this->error('Failed to fetch zones from Cloudflare: ' . $e->getMessage());
            return 1;
        }

        // Summary counters
        $totalChecked = 0;
        $totalRemoved = 0;
        $totalErrors = 0;
        $problemDomains = [];

        foreach ($domains as $domain) {
            $totalChecked++;
            $domainAge = Carbon::parse($domain->created_at)->diffInDays(Carbon::now());
            
            if ($this->getOutput()->isVerbose()) {
                $this->line(sprintf(
                    "\nChecking domain: %s (age: %d days)",
                    $domain->name,
                    $domainAge
                ));
            }

            try {
                $needsRemoval = false;
                $reason = '';

                // Check if domain exists in Cloudflare
                $zoneExists = $cloudflareZones->contains(function ($zone) use ($domain) {
                    return $zone->name === $domain->name;
                });

                if (!$zoneExists) {
                    $needsRemoval = true;
                    $reason = 'Domain not found in Cloudflare zones';
                } else {
                    // Try to get zone details as an additional validation
                    try {
                        $this->cloudFlareService->setZone($domain->name);
                        $this->cloudFlareService->getRecords($domain->name);
                    } catch (EndpointException $e) {
                        $needsRemoval = true;
                        $reason = 'Cannot access domain records in Cloudflare';
                    }
                }

                if ($needsRemoval) {
                    if ($this->getOutput()->isVerbose()) {
                        $this->warn("  ⚠ Problem detected: {$reason}");
                    }

                    $problemDomains[] = [
                        'name' => $domain->name,
                        'age' => $domainAge . ' days',
                        'created_at' => $domain->created_at,
                        'reason' => $reason
                    ];

                    if (!$isDryRun) {
                        $deleteResult = $this->domainService->deleteDomain($domain);
                        
                        if ($deleteResult['success']) {
                            $totalRemoved++;
                            if ($this->getOutput()->isVerbose()) {
                                $this->info('  ✓ Domain removed successfully');
                            }
                        } else {
                            $totalErrors++;
                            if ($this->getOutput()->isVerbose()) {
                                $this->error('  ✗ Failed to remove domain: ' . $deleteResult['message']);
                            }
                        }
                    }
                } else {
                    if ($this->getOutput()->isVerbose()) {
                        $this->line('  • Domain is accessible and valid');
                    }
                }
            } catch (Exception $e) {
                $totalErrors++;
                $this->error("Error processing {$domain->name}: " . $e->getMessage());
                Log::channel('cloudflare')->error('Error in cleanup process', [
                    'domain' => $domain->name,
                    'age' => $domainAge,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            // Show progress
            if (!$this->getOutput()->isVerbose()) {
                $this->output->write("\rProcessing domains... {$totalChecked}/{$totalDomains}");
            }
        }

        $this->output->newLine(2);
        
        // Show problem domains if any
        if (!empty($problemDomains)) {
            $this->info('=== Problem Domains ===');
            $this->table(
                ['Domain', 'Age', 'Created At', 'Reason'],
                $problemDomains
            );
        }

        // Display summary
        $this->info('=== Cleanup Summary ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Domains in System', $allDomainsCount],
                ['Domains Older than ' . $minAgeDays . ' days', $totalDomains],
                ['Domains Checked', $totalChecked],
                ['Domains to Remove', count($problemDomains)],
                ['Successfully Removed', $totalRemoved],
                ['Errors Encountered', $totalErrors],
            ]
        );

        if ($isDryRun) {
            $this->warn('This was a dry run. No domains were actually removed.');
        }

        Log::channel('cloudflare')->info('Domain cleanup completed', [
            'cutoff_date' => $cutoffDate->format('Y-m-d H:i:s'),
            'total_in_system' => $allDomainsCount,
            'total_checked' => $totalChecked,
            'total_removed' => $totalRemoved,
            'total_errors' => $totalErrors,
            'problem_domains' => $problemDomains
        ]);

        return 0;
    }
}