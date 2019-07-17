<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\DomainService;

class VerifyPendingDomains extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'verify:pendingdomains';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Verifica se os domínios pendentes ficaram ativos';
    /**
     * @var DomainService
     */
    private $domainService;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getDomainService()
    {
        if (!$this->domainService) {
            $this->domainService = app(DomainService::class);
        }

        return $this->domainService;
    }

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        try {

            $result = $this->getDomainService()->verifyPendingDomains();
        } catch (Exception $e) {
            Log::warning('VerifyPendingDomains - Erro no command ');
            report($e);
        }
    }
}
