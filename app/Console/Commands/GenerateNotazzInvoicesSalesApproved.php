<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Services\NotazzService;
use Illuminate\Support\Facades\Log;

class GenerateNotazzInvoicesSalesApproved extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'generate:notazzinvoicessalesapproved';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Gera as invoices de todas as vendas aprovadas de todos os projetos';

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
            $notazzService = new NotazzService();

            //gera primeiramente as invoices retroativas se existirem
            $notazzService->generateRetroactiveInvoices();

            //gera as invoices ainda nao geradas
            $notazzService->generateInvoicesSalesApproved();
        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));
        
    }
}
