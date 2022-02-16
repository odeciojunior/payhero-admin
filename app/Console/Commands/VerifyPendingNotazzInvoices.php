<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\NotazzService;

class VerifyPendingNotazzInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:pendingnotazzinvoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Faz o envio de todas as invoices pendentes e depois marca como completa as enviadas';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {
            $notazzService = new NotazzService();

            $notazzService->verifyPendingInvoices();
        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }
}
