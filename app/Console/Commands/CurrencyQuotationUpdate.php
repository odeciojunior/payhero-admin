<?php

namespace App\Console\Commands;

use App\Exceptions\CommandMonitorTimeException;
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
        $start = now();

        try {
            $currencyQuotationService = new CurrencyQuotationService();

            $currencyQuotationService->updateQuotations();
        } catch (Exception $e) {
            report($e);
        }

        $end = now();

        report(new CommandMonitorTimeException("command {$this->signature} começou as {$start} e terminou as {$end}"));
    }
}
