<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
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

        try {
            $notazzService = new NotazzService();

            $notazzService->verifyPendingInvoices();
        } catch (Exception $e) {
            report($e);
        }

    }
}
