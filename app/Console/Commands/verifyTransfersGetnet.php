<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\TransfersService;

class verifyTransfersGetnet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:transfersgetnet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'routine responsible for transferring the available money from the transactions to the users company registered getnet account';

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
        (new TransfersService())->verifyTransactionsGetnet();
    }
}
