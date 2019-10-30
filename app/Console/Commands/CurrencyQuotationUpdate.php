<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\CurrencyQuotationService;

class CurrencyQuotationUpdate extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'update:currencyquotation';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Atualiza as cotações das moedas';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    public function handle()
    {
        try {
            $currencyQuotationService = new CurrencyQuotationService();

            $currencyQuotationService->updateQuotations();
        } catch (Exception $e) {
            Log::warning('VerifyPendingDomains - Erro no command ');
            report($e);
        }
    }
}
