<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;

class MonthlyResume extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "command:name";

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
            $installmentsNumber = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
            $createdInit = "2021-11-01";
            $createdFinish = "2021-11-31";

            $this->line("Início: " . $createdInit . " fim: " . $createdFinish);

            foreach ($installmentsNumber as $installmentsNumber) {
                $this->line("VENDAS PARCELADAS EM " . $installmentsNumber . "X");

                $salescount = Sale::where("installments_amount", $installmentsNumber)
                    ->where("status", Sale::STATUS_APPROVED)
                    ->where("payment_method", Sale::CREDIT_CARD_PAYMENT)
                    ->where("created_at", ">", $createdInit)
                    ->where("created_at", "<", $createdFinish)
                    ->count();

                $this->line("Quantidade de vendas: " . $salescount);

                $value = Sale::where("installments_amount", $installmentsNumber)
                    ->where("status", Sale::STATUS_APPROVED)
                    ->where("payment_method", Sale::CREDIT_CARD_PAYMENT)
                    ->where("created_at", ">", $createdInit)
                    ->where("created_at", "<", $createdFinish)
                    ->sum("total_paid_value");

                $this->line('Valor total: R$ ' . foxutils()->formatMoney($value));

                $comission = Transaction::whereNull("company_id")
                    ->whereHas("sale", function ($q) use ($installmentsNumber, $createdInit, $createdFinish) {
                        $q->where("installments_amount", $installmentsNumber)
                            ->where("status", Sale::STATUS_APPROVED)
                            ->where("payment_method", Sale::CREDIT_CARD_PAYMENT)
                            ->where("created_at", ">", $createdInit)
                            ->where("created_at", "<", $createdFinish);
                    })
                    ->sum("value");

                $installmentsTaxValue = Sale::where("installments_amount", $installmentsNumber)
                    ->where("status", Sale::STATUS_APPROVED)
                    ->where("payment_method", Sale::CREDIT_CARD_PAYMENT)
                    ->where("created_at", ">", $createdInit)
                    ->where("created_at", "<", $createdFinish)
                    ->sum("interest_total_value");

                $this->line('Taxas de juros Nexuspay: R$ ' . foxutils()->formatMoney($installmentsTaxValue / 100));

                $this->line(
                    'Taxa base cobrada pela Nexuspay: R$ ' .
                        foxutils()->formatMoney(($comission - $installmentsTaxValue) / 100)
                );

                $this->line('Taxas totais cobradas pela Nexuspay: R$ ' . foxutils()->formatMoney($comission / 100));

                $gatewayTaxValue = Sale::where("installments_amount", $installmentsNumber)
                    ->where("status", Sale::STATUS_APPROVED)
                    ->where("payment_method", Sale::CREDIT_CARD_PAYMENT)
                    ->where("created_at", ">", $createdInit)
                    ->where("created_at", "<", $createdFinish)
                    ->sum("gateway_tax_value");

                $this->line('Taxas cobradas pela adquirente: R$ ' . foxutils()->formatMoney($gatewayTaxValue / 100));

                $this->line('Lucro: R$ ' . foxutils()->formatMoney(($comission - $gatewayTaxValue) / 100));
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
