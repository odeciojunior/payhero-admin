<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\CieloService;
use Modules\Core\Services\Gateways\GerencianetService;
use Modules\Core\Services\Gateways\GetnetService;

class UpdateAvailableBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'available-balance:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $defaultGateways = [
        AsaasService::class,
        //CieloService::class,
        GetnetService::class,
        GerencianetService::class
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        settings()->group('withdrawal_request')->set('withdrawal_request', false);

        foreach($this->defaultGateways as $gatewayClass) {
            $gatewayService = app()->make($gatewayClass);
            $gatewayService->updateAvailableBalance();
        }

        settings()->group('withdrawal_request')->set('withdrawal_request', true);
    }

}
