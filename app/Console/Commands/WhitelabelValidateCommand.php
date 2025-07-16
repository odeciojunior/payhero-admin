<?php

namespace App\Console\Commands;

use App\Facades\Whitelabel;
use Illuminate\Console\Command;

class WhitelabelValidateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whitelabel:validate {client?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate whitelabel configuration for a specific client or all clients';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $specificClient = $this->argument('client');
        
        if ($specificClient) {
            $this->validateClient($specificClient);
        } else {
            $this->validateAllClients();
        }
        
        return 0;
    }
    
    /**
     * Validate a specific client
     */
    private function validateClient($client)
    {
        $this->info("Validating client: {$client}");
        
        if (!Whitelabel::clientExists($client)) {
            $this->error("Client '{$client}' does not exist!");
            return;
        }
        
        $validation = Whitelabel::validateClientConfig($client);
        
        if ($validation['valid']) {
            $this->info("✅ Client '{$client}' configuration is valid!");
        } else {
            $this->error("❌ Client '{$client}' configuration is invalid!");
            $this->warn("Missing required properties: " . implode(', ', $validation['missing']));
        }
        
        // Test setting the client
        if (Whitelabel::setClient($client)) {
            $this->info("✅ Successfully set client to '{$client}'");
            $this->displayClientInfo();
        } else {
            $this->error("❌ Failed to set client to '{$client}'");
        }
    }
    
    /**
     * Validate all clients
     */
    private function validateAllClients()
    {
        $clients = Whitelabel::getAvailableClients();
        
        if (empty($clients)) {
            $this->error("No clients configured!");
            return;
        }
        
        $this->info("Validating " . count($clients) . " clients...\n");
        
        foreach ($clients as $client) {
            $this->validateClient($client);
            $this->line("");
        }
        
        $this->info("Current active client: " . Whitelabel::getCurrentClient());
    }
    
    /**
     * Display client information
     */
    private function displayClientInfo()
    {
        $client = Whitelabel::getCurrentClient();
        $config = Whitelabel::getCurrentClientConfig();
        
        $this->table(['Property', 'Value'], [
            ['Current Client', $client],
            ['App Name', Whitelabel::getAppName()],
            ['Primary Color', Whitelabel::getColor('primary', 'N/A')],
            ['Secondary Color', Whitelabel::getColor('secondary', 'N/A')],
            ['Main Logo', Whitelabel::getLogo('main')],
            ['Footer Text', Whitelabel::getFooterText()],
        ]);
    }
}
