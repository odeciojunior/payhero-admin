<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\TransfersService;


class VerifyTransfers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:transfers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify if has money to be transefered to users accounts daily';

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
        $transfersSerice = new TransfersService();
        $transfersSerice->verifyTransactions();
    }
}
