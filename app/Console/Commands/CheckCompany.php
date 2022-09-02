<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Exception;
use Modules\Core\Entities\Company;
use Modules\Core\Services\CompanyService;

class CheckCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "check:company";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

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
        try {

            $companyService = new CompanyService();

            $subThreeDays = Carbon::now()
                ->subDays(5)
                ->format("Y-m-d");

            $companies = Company::where('company_type', Company::JURIDICAL_PERSON)
            ->where("situation->situation_enum", 1)
            ->where("situation->date_check_situation", "<", $subThreeDays);

            $total = $companies->count();
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            $auxCount = 0;
            $auxService = 0;
            foreach ($companies->cursor() as $key=>$company) {
                $bar->advance();

                if ($auxService == 100) {
                    $companyService = new CompanyService();
                    $auxService = 0;
                }

                if(strlen(foxutils()->onlyNumbers($company->document)) == 14 ) {

                    $getCompany = $companyService->getCompanyByApiCNPJ($company->document);

                    if($getCompany) {

                        if(isset($getCompany['status']) and $getCompany['status'] == "ERROR"){

                            if((!strtolower(foxutils()->removeAccents($getCompany['message'])) ==  "cnpj invalido")) {

                                $mesage = 'returnSearch: ' . json_encode($getCompany);
                                $this->warn($mesage);
                                report(new Exception($mesage));
                                continue;

                            }
                            $getCompany['situacao'] = 'invalido';
                        }

                        $situation = $companyService->getSituation($getCompany['situacao']);

                        if($situation) {

                            $company->update([
                                'situation' => [
                                    'situation' => $situation['situation'],
                                    'situation_enum' => $situation['situation_enum'],
                                    'date_check_situation' => Carbon::now()->format("Y-m-d H:i:s"),
                                ]
                            ]);
                        }
                    }
                    sleep(21);
                } else {
                    $situation = $companyService->getSituation('invalido ');
                    $company->update([
                        'situation' => [
                            'situation' => $situation['situation'],
                            'situation_enum' => $situation['situation_enum'],
                            'date_check_situation' => Carbon::now()->format("Y-m-d H:i:s"),
                        ]
                    ]);
                }

                $auxCount ++;
                $auxService ++;
            }
            $bar->finish();
        } catch (Exception $e) {
            report($e);
        }
    }
}
