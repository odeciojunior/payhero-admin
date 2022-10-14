<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\Pipefy\PipefyService;

class PipefyUpdateCard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "pipefy:update-card";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Pipefy Update Card";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (FoxUtils::isProduction()) {
            //Atualizar todos os cards com o Label Facebook Ads ou Google Ads
            $users = User::whereNotNull("pipefy_card_id")->where("created_at", ">=", "2022-09-01 00:00:00");
            foreach ($users->cursor() as $user) {
                $labelAd = "";
                if (!empty($user->utm_srcs)) {
                    $utmSrcs = json_decode($user->utm_srcs, true);
                    if (!empty($utmSrcs["utm_source"])) {
                        if ($utmSrcs["utm_source"] == "google_ads") {
                            $labelAd = PipefyService::LABEL_GOOGLE_ADS;
                        } elseif ($utmSrcs["utm_source"] == "facebook_ads") {
                            $labelAd = PipefyService::LABEL_FACEBOOK_ADS;
                        }
                    }
                }
                (new PipefyService())->updateCardLabel($user, [$labelAd]);
            }

            dd("Finalizado atualização das TAGs Facebook ADs ou Google ADs");


            //Criar Card no Pipe Gerenciamento 100k ou monitoriamento -100k (Apenas para os usuários que não estão no pipefy)
            $users = User::whereNotNull("users.total_commission_value")
                ->whereNull("users.pipefy_card_id")
                //                ->where("total_commission_value", ">", "1000000")
                ->where("total_commission_value", ">=", "10000000");

            foreach ($users->cursor() as $user) {
                if ($user->total_commission_value >= 10000000) {
                    (new PipefyService())->createCardUserNewPipe($user, PipefyService::PIPE_MORE_100k);
                }
            }

            $date = Carbon::today()->subDays(180);

            $users = User::selectRaw("users.*")
                ->selectRaw(
                    "(  SELECT SUM(t.value) FROM transactions as t
                    JOIN companies as c ON c.id = t.company_id
                    WHERE t.user_id = users.id and t.status_enum IN (1,2)
                        AND t.created_at > '{$date}'
                    GROUP BY t.user_id ) as total_sale"
                )
                ->whereNotNull("users.pipefy_card_id");

            foreach ($users->cursor() as $user) {
                $labelAd = "";
                if (!empty($user->utm_srcs)) {
                    $utmSrcs = json_decode($user->utm_srcs, true);
                    if (!empty($utmSrcs["utm_source"])) {
                        if ($utmSrcs["utm_source"] == "google_ads") {
                            $labelAd = PipefyService::LABEL_GOOGLE_ADS;
                        } elseif ($utmSrcs["utm_source"] == "facebook_ads") {
                            $labelAd = PipefyService::LABEL_FACEBOOK_ADS;
                        }
                    }
                }
                if (empty($user->total_sale)) {
                    $phase = json_decode($user->pipefy_card_data);
                    if (!empty($phase->phase) && $phase->phase == PipefyService::PHASE_ACTIVE_AND_SELLING) {
                        (new PipefyService())->updateCardLabel($user, [PipefyService::LABEL_WITHOUT_SELLING, $labelAd]); //30 dias sem vender
                    }
                } elseif ($user->total_sale > 0) {
                    (new PipefyService())->moveCardToPhase($user, PipefyService::PHASE_ACTIVE_AND_SELLING);
                    (new PipefyService())->updateCardLabel($user, [
                        PipefyService::LABEL_SALES_BETWEEN_0_100k,
                        $labelAd,
                    ]);
                }
            }

            $date = Carbon::today()->subDays(180);

            $transactionModel = new Transaction();
            $transactionPresent = $transactionModel->present();
            $transactions = User::join("transactions", "users.id", "transactions.user_id")
                ->join("companies", "companies.id", "transactions.company_id")
                ->whereIn("transactions.status_enum", [
                    $transactionPresent->getStatusEnum("paid"),
                    $transactionPresent->getStatusEnum("transfered"),
                ])
                ->whereNotNull("users.pipefy_card_id")
                ->where("transactions.created_at", ">", $date)
                ->groupBy("companies.user_id")
                ->selectRaw("companies.user_id, SUM(transactions.value) as value");

            foreach ($transactions->cursor() as $transaction) {
                $user = User::where("id", $transaction->user_id)->first();
                $labelAd = "";
                if (!empty($user->utm_srcs)) {
                    $utmSrcs = json_decode($user->utm_srcs, true);
                    if (!empty($utmSrcs["utm_source"])) {
                        if ($utmSrcs["utm_source"] == "google_ads") {
                            $labelAd = PipefyService::LABEL_GOOGLE_ADS;
                        } elseif ($utmSrcs["utm_source"] == "facebook_ads") {
                            $labelAd = PipefyService::LABEL_FACEBOOK_ADS;
                        }
                    }
                }
                if ($transaction->value >= 10000000) {
                    (new PipefyService())->moveCardToPhase($user, PipefyService::PHASE_ACTIVE_AND_SELLING);
                    (new PipefyService())->updateCardLabel($user, [
                        PipefyService::LABEL_SALES_BETWEEN_100k_1M,
                        $labelAd,
                    ]);
                } else {
                    (new PipefyService())->updateCardLabel($user, [$labelAd]);
                }
            }

            dd("Finalizado!");

            DB::table("users")
                ->whereNotNull("pipefy_card_id")
                ->update(["pipefy_card_data" => null]);

            //Criar todos os Cards que não foram criados desde do dia 1 de setempro
            $users = User::whereNull("pipefy_card_id")
                ->where("created_at", ">=", "2022-09-01 00:00:00")
                ->get();
            foreach ($users as $user) {
                (new PipefyService())->createCardUser($user);
            }

            //Atualizar todos os cards com o Label Facebook Ads ou Google Ads
            $users = User::whereNotNull("pipefy_card_id")->where("created_at", ">=", "2022-09-01 00:00:00");
            foreach ($users->cursor() as $user) {
                $labelAd = "";
                if (!empty($user->utm_srcs)) {
                    $utmSrcs = json_decode($user->utm_srcs, true);
                    if (!empty($utmSrcs["utm_source"])) {
                        if ($utmSrcs["utm_source"] == "google_ads") {
                            $labelAd = PipefyService::LABEL_GOOGLE_ADS;
                        } elseif ($utmSrcs["utm_source"] == "facebook_ads") {
                            $labelAd = PipefyService::LABEL_FACEBOOK_ADS;
                        }
                    }
                }
                (new PipefyService())->updateCardUserinformations($user);
                (new PipefyService())->updateCardLabel($user, [$labelAd]);
            }

            //Criar Card no Pipe Gerenciamento 100k ou monitoriamento -100k (Apenas para os usuários que não estão no pipefy)
            $users = User::whereNotNull("users.total_commission_value")
                ->whereNull("users.pipefy_card_id")
                ->where("total_commission_value", ">", "1000000")
                ->where("total_commission_value", "<", "10000000");

            foreach ($users->cursor() as $user) {
                if ($user->total_commission_value >= 10000000) {
                    (new PipefyService())->createCardUserNewPipe($user, PipefyService::PIPE_MORE_100k);
                } elseif ($user->total_commission_value <= 10000000 && $user->total_commission_value > 1000000) {
                    (new PipefyService())->createCardUserNewPipe($user, PipefyService::PIPE_LESS_100k);
                }
            }

            $this->checkDocumentUser();

            dd("Finalizado");
            exit();

            //        $this->createCardFailt();
            //        $this->updateCardInformation();
            $this->checkDocumentUser();
        }
    }

    public function updateCardlabel()
    {
        $pipefyService = new PipefyService();

        $users = User::whereNotNull("users.pipefy_card_id");

        foreach ($users->cursor() as $user) {
            if ($user->total_commission_value > 0) {
                if ($user->total_commission_value < 10000000) {
                    (new PipefyService())->updateCardLabel($user, [PipefyService::LABEL_SALES_BETWEEN_0_100k]);
                } elseif ($user->total_commission_value < 100000000) {
                    (new PipefyService())->updateCardLabel($user, [
                        PipefyService::LABEL_SALES_BETWEEN_0_100k,
                        PipefyService::LABEL_SALES_BETWEEN_100k_1M,
                    ]);
                } elseif ($user->total_commission_value < 1000000000) {
                    (new PipefyService())->updateCardLabel($user, [
                        PipefyService::LABEL_SALES_BETWEEN_0_100k,
                        PipefyService::LABEL_SALES_BETWEEN_100k_1M,
                        PipefyService::LABEL_SALES_BETWEEN_1M_10M,
                    ]);
                } elseif ($user->total_commission_value < 2500000000) {
                    (new PipefyService())->updateCardLabel($user, [
                        PipefyService::LABEL_SALES_BETWEEN_0_100k,
                        PipefyService::LABEL_SALES_BETWEEN_100k_1M,
                        PipefyService::LABEL_SALES_BETWEEN_1M_10M,
                        PipefyService::LABEL_SALES_BETWEEN_10M_25M,
                    ]);
                } elseif ($user->total_commission_value < 5000000000) {
                    (new PipefyService())->updateCardLabel($user, [
                        PipefyService::LABEL_SALES_BETWEEN_0_100k,
                        PipefyService::LABEL_SALES_BETWEEN_100k_1M,
                        PipefyService::LABEL_SALES_BETWEEN_1M_10M,
                        PipefyService::LABEL_SALES_BETWEEN_10M_25M,
                        PipefyService::LABEL_SALES_BETWEEN_25M_50M,
                    ]);
                } elseif ($user->total_commission_value >= 5000000000) {
                    (new PipefyService())->updateCardLabel($user, [
                        PipefyService::LABEL_SALES_BETWEEN_0_100k,
                        PipefyService::LABEL_SALES_BETWEEN_100k_1M,
                        PipefyService::LABEL_SALES_BETWEEN_1M_10M,
                        PipefyService::LABEL_SALES_BETWEEN_10M_25M,
                        PipefyService::LABEL_SALES_BETWEEN_25M_50M,
                        PipefyService::LABEL_SALES_OVER_50M,
                    ]);
                }
            }
        }
    }

    public function checkDocumentUser()
    {
        $pipefyService = new PipefyService();

        $usersDocument = User::selectRaw("users.*")
            ->selectRaw(
                "(
                        SELECT count( c.id ) FROM companies AS c
                            WHERE c.user_id = users.id
                                AND (c.address_document_status = " .
                CompanyDocument::STATUS_REFUSED .
                "
                                OR c.contract_document_status = " .
                CompanyDocument::STATUS_REFUSED .
                " )
                            GROUP BY c.user_id
                        ) total_companies_refused"
            )
            ->selectRaw(
                "(
                        SELECT count( c.id ) FROM companies AS c
                            WHERE c.user_id = users.id
                                AND (c.address_document_status = " .
                CompanyDocument::STATUS_APPROVED .
                "
                                OR c.contract_document_status = " .
                CompanyDocument::STATUS_APPROVED .
                " )
                            GROUP BY c.user_id
                        ) total_companies_active"
            )
            ->whereNotNull("users.pipefy_card_id");

        $total = $usersDocument->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        foreach ($usersDocument->cursor() as $user) {
            if (
                $user->address_document_status == UserDocument::STATUS_REFUSED ||
                $user->personal_document_status == UserDocument::STATUS_REFUSED
            ) {
                if (empty($user->total_companies_active)) {
                    $pipefyService->moveCardToPhase($user, PipefyService::PHASE_REFUSED_DOCUMENT);
                } elseif (!empty($user->companies)) {
                    foreach ($user->companies as $companie) {
                        if (
                            $companie->address_document_status == CompanyDocument::STATUS_REFUSED &&
                            $companie->contract_document_status == CompanyDocument::STATUS_REFUSED
                        ) {
                            $pipefyService->moveCardToPhase($user, PipefyService::PHASE_REFUSED_DOCUMENT);
                        }
                    }
                }
            } elseif (
                $user->account_is_approved == User::STATUS_ACTIVE &&
                ($user->address_document_status == UserDocument::STATUS_APPROVED ||
                    $user->personal_document_status == UserDocument::STATUS_APPROVED)
            ) {
                if (!empty($user->companies)) {
                    foreach ($user->companies as $companie) {
                        if (
                            $companie->address_document_status == CompanyDocument::STATUS_APPROVED &&
                            $companie->contract_document_status == CompanyDocument::STATUS_APPROVED
                        ) {
                            if ($user->total_commission_value > 0) {
                                $pipefyService->moveCardToPhase($user, PipefyService::PHASE_ACTIVE_AND_SELLING);
                                $this->updateCardlabel();
                            } else {
                                $pipefyService->moveCardToPhase($user, PipefyService::PHASE_ACTIVE);
                            }
                        }
                    }
                }
            }
            $bar->advance();
        }
        $bar->finish();
    }

    public function createCardFailt()
    {
        $pipefyService = new PipefyService();
        $usersNews = User::where("total_commission_value", ">", "20000000")
            ->orderBy("created_at", "DESC")
            ->limit(7)
            ->get();
        foreach ($usersNews as $teste) {
            $pipefyService->createCardUser($teste);
        }
    }

    public function updateCardInformation()
    {
        $pipefyService = new PipefyService();

        $users = User::whereNotNull("users.pipefy_card_id");

        $total = $users->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        foreach ($users->cursor() as $user) {
            $pipefyService->updateCardUserinformations($user);
            $pipefyService->updateAssignee($user);
            $bar->advance();
        }
        $bar->finish();
    }

    public function checkFirstSale()
    {
        $pipefyService = new PipefyService();
        $labelArray = [PipefyService::LABEL_SOLD];

        $users = User::select("users.*")
            ->join("tasks_users as t", "users.id", "t.user_id")
            ->whereNotNull("users.pipefy_card_id")
            ->where("task_id", Task::TASK_FIRST_SALE)
            ->get();

        foreach ($users as $user) {
            $pipefyService->moveCardToPhase($user, PipefyService::PHASE_ACTIVE);
            $pipefyService->moveCardToPhase($user, PipefyService::PHASE_ACTIVE_AND_SELLING);
        }
    }
}
