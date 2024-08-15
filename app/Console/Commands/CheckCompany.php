<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
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
        $companyService = new CompanyService();

        $subThreeDays = Carbon::now()
            ->subDays(5)
            ->format("Y-m-d");

        $companies = Company::where("company_type", Company::JURIDICAL_PERSON)
            ->where("situation->situation_enum", 1)
            ->where("situation->date_check_situation", "<", $subThreeDays);

        $total = $companies->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $companies->chunk(100, function ($companies) use ($companyService, $bar) {
            foreach ($companies as $company) {
                try {
                    $cleanDocument = foxutils()->onlyNumbers($company->document);
                    if (strlen($cleanDocument) == 14) {
                        $getCompany = $companyService->getCompanyByApiCNPJ($company->document);

                        if ($getCompany) {
                            if (
                                isset($getCompany["message"]) &&
                                !strtolower(foxutils()->removeAccents($getCompany["message"])) ==
                                    "cnpj $cleanDocument invalido"
                            ) {
                                $message = "returnSearch: " . json_encode($getCompany);
                                $this->warn($message);
                                throw new Exception($message);
                            }

                            $situation = isset($getCompany["situacaoCadastral"])
                                ? $companyService->getSituation($getCompany["situacaoCadastral"]["codigo"])
                                : null;

                            if ($situation) {
                                $company->update([
                                    "situation" => [
                                        "situation" => $situation["situation"],
                                        "situation_enum" => $situation["situation_enum"],
                                        "date_check_situation" => Carbon::now()->format("Y-m-d H:i:s"),
                                        "company_data" => $getCompany,
                                    ],
                                ]);
                            }
                        }
                        sleep(1);
                    } else {
                        $situation = $companyService->getSituation("invalido ");
                        $company->update([
                            "situation" => [
                                "situation" => $situation["situation"],
                                "situation_enum" => $situation["situation_enum"],
                                "date_check_situation" => Carbon::now()->format("Y-m-d H:i:s"),
                            ],
                        ]);
                    }
                } catch (Exception $e) {
                    report($e);
                    $this->error("Error: " . $e->getMessage());
                }

                $bar->advance();
            }
        });

        $bar->finish();
    }
}
