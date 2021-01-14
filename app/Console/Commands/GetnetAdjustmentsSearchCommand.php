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

        //$company = Company::find(3035); // Rupert
        $company = Company::find(2265); // ATAUA SIMOES DOS SANTOS 01210892073
        $company = Company::find(2629); // JUSTGO GLOBAL INTERMEDIAÇÃO DE NEGÓCIOS DE VENDAS LTDA
        $g = new GetnetBackOfficeService();
        $result = $g->getDiscounts($company);

        dd($result);
        return 0;
    }
}
