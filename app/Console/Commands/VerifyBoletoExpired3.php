<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\BoletoService;

class VerifyBoletoExpired3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:boletoexpired3';

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $boletoService = new BoletoService();
        $boletoService->verifyBoletoExpired3();
    }
}
