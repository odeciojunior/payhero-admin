<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyBankAccount;
use Modules\Core\Services\BoletoService;
use Modules\Core\Services\DemoAccount\DemoFakeDataService;
use Modules\Core\Services\Gateways\Safe2PayService;
use Modules\Core\Services\PixService;
use Modules\Notazz\Http\Controllers\NotazzController;
use ParagonIE\Sodium\Compat;

class DemoAccountFakeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo-account:fake-data';

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
     * @return int
     */
    public function handle()
    {
        Config::set('database.default', 'demo');

        $demo =  new DemoFakeDataService();
        
        // $demo->createFakeTicket();

        // $demo->verifyAbandonedCarts();
        
    }        

}
