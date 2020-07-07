<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserInformation;
use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\UserService;

class SetAccountTypeToCompany extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'SetAccountTypeToCompany';
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

            $companies = $companyModel->get();

            foreach ($companies as $company) {
                $company->update(['account_type' => 1]);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
