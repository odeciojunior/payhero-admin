<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Services\DomainService;
use Illuminate\Support\Facades\Log;

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

    public function handle()
    {

        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {
            $this->getDomainService()->verifyPendingDomains();
        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }
}
