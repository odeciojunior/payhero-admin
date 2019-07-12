<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\BoletoExpireService;
use Modules\Core\Services\BoletoExpiringService;
use Modules\Core\Services\BoletoService;

class SendEmailBoletoExpiring extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'verify:boletoexpiring';
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
     * @return mixed
     */
    public function handle()
    {
        $boletoService = new BoletoService();
        $boletoService->verifyBoletosExpiring();
    }
}
