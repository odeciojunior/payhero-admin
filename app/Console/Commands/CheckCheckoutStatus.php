<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\CheckoutService;

class CheckCheckoutStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:checkout-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check checkout status';

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
        $checkoutService = new CheckoutService();

        $checkoutService->verifyCheckoutStatus();
    }

}
