<?php

namespace App\Console\Commands;

use Exception;
use App\Exceptions\CommandMonitorTimeException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\BoletoService;

class ChangeBoletoPendingToCanceled extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:boletopendingtocanceled';

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
            $boletoService->changeBoletoPendingToCanceled();

        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }
}
