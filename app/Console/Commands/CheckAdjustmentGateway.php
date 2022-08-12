<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\CompanyAdjustments;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\GetnetBackOfficeService;

class CheckAdjustmentGateway extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "check:adjustment-gateway";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Check Adjustment Gateway";
    public $gateways = [Gateway::GETNET_PRODUCTION_ID];

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
            $companies = Company::with("gatewayCompanyCredential")
                ->whereHas("gatewayCompanyCredential")
                //->onlyTrashed()
                ->withTrashed()
                ->where("id", ">=", 3000)
                ->where("id", "<=", 5000);

            $total = $companies->count();
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            foreach ($companies->cursor() as $key => $company) {
                $this->line("  Company: " . $company->id . " CompanyName: " . $company->fantasy_name);
                //continue;
                foreach ($this->gateways as $gateway) {
                    switch ($gateway) {
                        case Gateway::GETNET_PRODUCTION_ID:
                            if (
                                GatewaysCompaniesCredential::where("company_id", $company->id)
                                    ->where("gateway_id", Gateway::GETNET_PRODUCTION_ID)
                                    ->whereNotNull("gateway_subseller_id")
                                    ->exists()
                            ) {
                                $this->checkGetnet($company);
                            }

                            break;
                    }
                }
                $bar->advance();
            }

            $bar->finish();
        } catch (Exception $e) {
            dump($e);
            report($e);
        }
    }

    public function checkGetnet(Company $company)
    {
        $getnetService = new GetnetBackOfficeService();
        $data = Carbon::createFromFormat("d/m/Y", "05/02/2022");
        //        $data = Carbon::createFromFormat('d/m/Y', '05/11/2021');
        $aux = 0;

        $adjustments = [];
        $value_total = 0;

        while ($data->lessThan(Carbon::now())) {
            if ($aux == 50) {
                $getnetService = new GetnetBackOfficeService();
                $aux = 0;
                sleep(1);
            }
            $aux++;

            $response = $getnetService
                ->setStatementSubSellerId(CompanyService::getSubsellerId($company))
                ->setStatementStartDate($data)
                ->setStatementEndDate($data->addDays(1))
                ->setStatementDateField("schedule")
                ->getStatement();

            $gatewaySale = json_decode($response);

            if (isset($gatewaySale->adjustments) && count($gatewaySale->adjustments) > 0) {
                foreach ($gatewaySale->adjustments as $adjustment) {
                    $adjustment_reason = strtolower($adjustment->adjustment_reason ?? "");

                    // +"adjustment_reason": "015|Ajuste GetNet - Saldo Negativo - Credito para Liquidacao Negativa Expirada ha 5 dias"
                    if (
                        !str_contains(
                            foxutils()->removeAccents($adjustment_reason),
                            foxutils()->removeAccents(strtolower("Ajuste GetNet"))
                        )
                    ) {
                        continue;
                    }

                    //                    if($adjustment->adjustment_type !=  6) {
                    //                        continue;
                    //                    }

                    //dump($adjustment);

                    if (!in_array($adjustment, $adjustments)) {
                        $adjustments[] = $adjustment;

                        if (
                            CompanyAdjustments::where("company_id", $company->id)
                                ->where("adjustment_id", $adjustment->adjustment_id)
                                ->exists()
                        ) {
                            continue;
                        }

                        if (!empty($companyAdjustment)) {
                            $value_total = (int) $companyAdjustment->adjustment_amount_total;
                        }

                        $companyAdjustment = CompanyAdjustments::select("adjustment_amount_total")
                            ->where("company_id", $company->id)
                            ->latest()
                            ->first();
                        //$companyAdjustment = CompanyAdjustments::select('adjustment_amount_total')->where('company_id', $company->id)->where('adjustment_id', (int)$adjustment->adjustment_id)->latest()->first();

                        if (!empty($companyAdjustment)) {
                            $value_total = (int) $companyAdjustment->adjustment_amount_total;
                        }

                        if ($adjustment->transaction_sign == "+") {
                            $value_total += (int) $adjustment->adjustment_amount;
                        } else {
                            $value_total -= (int) $adjustment->adjustment_amount;
                        }

                        CompanyAdjustments::create([
                            "company_id" => $company->id,
                            "adjustment_id" => (int) $adjustment->adjustment_id,
                            "adjustment_amount" => $adjustment->adjustment_amount,
                            "transaction_sign" => $adjustment->transaction_sign == "+" ? "+" : "-",
                            "adjustment_type" => $adjustment->adjustment_type,
                            "adjustment_amount_total" => $value_total,
                            "adjustment_reason" => $adjustment->adjustment_reason,
                            "date_adjustment" => Carbon::parse($adjustment->adjustment_date),
                            "subseller_rate_closing_date" => !empty($adjustment->subseller_rate_confirm_date)
                                ? Carbon::parse($adjustment->subseller_rate_confirm_date)
                                : null,
                            "data" => json_encode($adjustment),
                        ]);
                    }
                }
            }
        }
        //dump($adjustments);
        return null;
    }
}
