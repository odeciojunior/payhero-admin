<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Services\CartRecoveryService;

class VerifyAbandonedCarts extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'verify:abandonedcarts';
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

    public function handle()
    {

        try {

            $cartRecoveryService = new CartRecoveryService();
            $cartRecoveryService->verifyAbandonedCarts();

        } catch (Exception $e) {
            report($e);
        }

    }
}
