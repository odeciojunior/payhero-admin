<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Services\GetnetBackOfficeService;

class GetnetGetDiscountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getnet:get-discounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza os descontos para uma company';

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

        //$companyId = 2964; // JOÃO 28 ou 2964
        //$companyId = 3035; // RUPERT BRASIL LUSTOSA 00110115309
        $companyId = $this->ask('Qual a company_id?', 0);

        $company = Company::select('id', 'user_id', 'fantasy_name', 'subseller_getnet_id',
            'subseller_getnet_homolog_id')
            ->find($companyId);

        $getnetBackOfficeService = new GetnetBackOfficeService();
        $data = $getnetBackOfficeService->saveDiscountsInDatabase($company);
        dd($data);
        return 0;
    }
}
