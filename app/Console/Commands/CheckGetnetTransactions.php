<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\TransactionsService;

class CheckGetnetTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:getnet-transactions';

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
        (new TransactionsService())->verifyTransactions();
    }
}
