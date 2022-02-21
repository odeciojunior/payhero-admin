<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Services\BoletoService;
use Illuminate\Support\Facades\Log;

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

        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {

            $boletoService = new BoletoService();
            $boletoService->verifyBoletoWaitingPayment();

        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }
}
