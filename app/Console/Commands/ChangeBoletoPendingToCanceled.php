<?php

namespace App\Console\Commands;

use App\Exceptions\CommandMonitorTimeException;
use Illuminate\Console\Command;
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
        $start = now();

        $boletoService = new BoletoService();
        $boletoService->changeBoletoPendingToCanceled();

        $end = now();

        report(new CommandMonitorTimeException("command {$this->signature} começou as {$start} e terminou as {$end}"));
    }
}
