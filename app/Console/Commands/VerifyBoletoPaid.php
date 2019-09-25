<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\BoletoService;

class VerifyBoletoPaid extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'verify:boletopaid';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        /** @var BoletoService $boletoService */
        $boletoService = new BoletoService();
        $boletoService->verifyBoletoPaid();
    }
}
