<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\GerencianetService;
use Modules\Core\Services\Gateways\GetnetService;
use Modules\Core\Services\Gateways\IuguService;
use Modules\Core\Services\Gateways\Safe2PayService;

class UpdateAvailableBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "available-balance:update";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

    private $defaultGateways = [
        IuguService::class,
        Safe2PayService::class,
        // AsaasService::class,
        // GerencianetService::class,
        // GetnetService::class,
        //CieloService::class,
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
        try {
            settings()
                ->group("withdrawal_request")
                ->set("withdrawal_request", false);

            foreach ($this->defaultGateways as $gatewayClass) {
                $gatewayService = app()->make($gatewayClass);
                $gatewayService->updateAvailableBalance();
            }

            settings()
                ->group("withdrawal_request")
                ->set("withdrawal_request", true);
        } catch (Exception $e) {
            report($e);
        }
    }
}
