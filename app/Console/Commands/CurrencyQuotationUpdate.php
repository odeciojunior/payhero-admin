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

    public function handle()
    {

        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {
            $currencyQuotationService = new CurrencyQuotationService();

            $currencyQuotationService->updateQuotations();
        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }
}
