<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckSaleContestationsTxtFormat extends Command
{
    protected $signature = 'getnet:import-sale-contestations-txt-format';

    protected $description = 'Import sale contestation from gmail';

    protected $cloudfoxCode = "7762088";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return 0;
    }
}
