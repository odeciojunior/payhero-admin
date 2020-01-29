<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Exception;

class UpdateCompanyDocumentRemoveMask extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'command:UpdateCompanyDocumentRemoveMask';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        try {
            $companyModel = new Company();

            $companies = $companyModel->whereNotNull('company_document')->get();

            foreach ($companies as $company) {
                $company->update([
                                     'company_document' => preg_replace("/[^0-9]/", "", $company->company_document),
                                 ]);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
