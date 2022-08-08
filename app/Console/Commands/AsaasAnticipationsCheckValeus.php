<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\Gateways\AsaasService;
use Illuminate\Support\Facades\Log;

class AsaasAnticipationsCheckValeus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "asaas:anticipations-check-values";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

    public $saveRequests = false;
    public $simulate = true;

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
     * @return void
     */
    public function handle()
    {
        try {
            $service = new AsaasService();

            $toDay = Carbon::now()->format("Y-m-d");
            $afterThreeDays = Carbon::now()
                ->addDays(3)
                ->format("Y-m-d");

            $transactions = Transaction::with("sale")
                ->whereHas("sale", function ($query) {
                    $query->whereNull("anticipation_status");
                    $query->where("payment_method", Sale::CREDIT_CARD_PAYMENT);
                })
                ->where("gateway_id", Gateway::ASAAS_PRODUCTION_ID)
                ->whereIn("status_enum", [Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED])
                ->whereNotNull("company_id")
                ->where("type", Transaction::TYPE_PRODUCER)
                ->where("release_date", "<=", $afterThreeDays)
                ->where("created_at", "<", $toDay);

            $total = $transactions->count();
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            $cannotAnticipate = [];
            foreach ($transactions->cursor() as $transaction) {
                $sale = $transaction->sale;
                $response = $service->makeAnticipation($sale, $this->saveRequests, $this->simulate);

                if (isset($response["errors"])) {
                    // se ja a mensagem tem no array entra no if
                    if (isset($response["errors"][0]["code"])) {
                        $description = strtolower(trim($response["errors"][0]["description"]));

                        if (
                            str_contains(
                                $description,
                                "limite para antecipação de cartão de crédito e o valor escolhido"
                            )
                        ) {
                            $description = "limite para antecipação de cartão de crédito e o valor escolhido";
                            $bar->advance();
                            continue;
                        }

                        if (str_contains($description, "este recebível já está reservado para a institui")) {
                            $description = "este recebível já está reservado para a institui";
                            $bar->advance();
                            continue;
                        }

                        if (isset($cannotAnticipate[$description])) {
                            // se ja o user tem no array entra no if
                            if (isset($cannotAnticipate[$description][$sale->owner_id])) {
                                $cannotAnticipate[$description][$sale->owner_id]["count_sale"] += 1;
                                $cannotAnticipate[$description][$sale->owner_id]["value"] += $transaction->value;
                                array_push(
                                    $cannotAnticipate[$description][$sale->owner_id]["sale_ids"],
                                    $transaction->sale_id
                                );
                                array_push(
                                    $cannotAnticipate[$description][$sale->owner_id]["values"],
                                    $transaction->value
                                );
                                array_push(
                                    $cannotAnticipate[$description][$sale->owner_id]["messages"],
                                    strtolower(trim($response["errors"][0]["description"]))
                                );
                            } else {
                                $cannotAnticipate[$description][$sale->owner_id] = [
                                    "user_id" => $transaction->user->id,
                                    "user_name" => $transaction->user->name,
                                    "company_id" => $transaction->company->id,
                                    "company_name" => $transaction->company->fantasy_name,
                                    "count_sale" => 1,
                                    "value" => $transaction->value,
                                    "sale_ids" => [$transaction->sale_id],
                                    "values" => [$transaction->value],
                                    "messages" => [strtolower(trim($response["errors"][0]["description"]))],
                                ];
                            }
                        } else {
                            $cannotAnticipate[$description][$sale->owner_id] = [
                                "user_id" => $transaction->user->id,
                                "user_name" => $transaction->user->name,
                                "company_id" => $transaction->company->id,
                                "company_name" => $transaction->company->fantasy_name,
                                "count_sale" => 1,
                                "value" => $transaction->value,
                                "sale_ids" => [$transaction->sale_id],
                                "values" => [$transaction->value],
                                "messages" => [strtolower(trim($response["errors"][0]["description"]))],
                            ];
                        }
                    }
                }

                $bar->advance();
            }
            Log::info("--------------------------------------------------------------------------------");
            Log::info(print_r($cannotAnticipate, true));

            $bar->finish();
        } catch (Exception $e) {
            report($e);
        }
    }
}
