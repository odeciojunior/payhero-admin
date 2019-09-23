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
     * @return mixed
     */
    public function handle()
    {
        try {

            $notazzService = new NotazzService();

            $notazzService->verifyPendingInvoices();

        } catch (Exception $e) {
            Log::warning('VerifyPendingNotazzInvoices - Erro no command ');
            report($e);
        }
    }
}
