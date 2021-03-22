<?php

namespace App\Console\Commands;

use App\Exceptions\CommandMonitorTimeException;
use Illuminate\Console\Command;
use Modules\Core\Services\BoletoService;

class VerifyBoletoWaitingPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:boletowaitingpayment';

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
        $boletoService = new BoletoService();
        $boletoService->verifyBoletoWaitingPayment();
    }
}
