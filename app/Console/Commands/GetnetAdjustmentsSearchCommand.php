<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Services\GetnetBackOfficeService;

class GetnetAdjustmentsSearchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getnet:adjustments-search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica valores de ajustes na GetNet a partir do saque realizado';

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

        $company = Company::find(3035);
        $g = new GetnetBackOfficeService();
        $result = $g->getAdjustments($company);

        dd($result);
        return 0;
    }
}
