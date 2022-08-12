<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Services\NotazzService;

class GenerateNotazzInvoicesSalesApproved extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = "generate:notazzinvoicessalesapproved";
    /**
     * The console command description.
     * @var string
     */
    protected $description = "Gera as invoices de todas as vendas aprovadas de todos os projetos";

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
        try {
            $notazzService = new NotazzService();

            //gera primeiramente as invoices retroativas se existirem
            $notazzService->generateRetroactiveInvoices();

            //gera as invoices ainda nao geradas
            $notazzService->generateInvoicesSalesApproved();
        } catch (Exception $e) {
            report($e);
        }
    }
}
