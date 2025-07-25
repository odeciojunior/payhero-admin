<?php

namespace App\Console\Commands\Demo;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Modules\Core\Services\BoletoService;
use Modules\Core\Services\Gateways\Safe2PayService;
use Modules\Core\Services\PixService;

class UpdateFinancialFakeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:update-financial-fake-data';

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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Config::set('database.default', 'demo');

        $gatewayService = new Safe2PayService();
        $gatewayService->updateAvailableBalance();

        $boletoService = new BoletoService();
        $boletoService->changeBoletoPendingToCanceled();

        $pixService = new PixService();
        $pixService->changePixToCanceled();

    }
}
