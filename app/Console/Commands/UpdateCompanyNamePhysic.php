<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Company;

class UpdateCompanyNamePhysic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateCompanyNamePhysic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza nomes das empresas pessoa física para o nome do usuario';

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
        try {
            
            $companies = Company::with('user')->where('company_type', 1)->get();

            foreach ($companies as $company) {
                if(!empty($company->user->name)) {
                    $company->update(['fantasy_name' => $company->user->name]);
                }
            }
        } catch (Exception $e) {
            report($e);
        } 
    }
}
