<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Entities\Task;
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

            dd("Finalizado");
            exit();

            //        $this->createCardFailt();
            //        $this->updateCardInformation();
            //        $this->checkDocumentUser();
        }
    }

    public function updateCardlabel()
    {
        $pipefyService = new PipefyService();

        $users = User::whereNotNull("users.pipefy_card_id");

        foreach ($users->cursor() as $user) {
            if ($user->total_commission_value > 0) {
                if ($user->total_commission_value < 10000000) {
                    $pipefyService->updateCardLabel($user, [PipefyService::LABEL_SALES_BETWEEN_0_100k]);
                } elseif ($user->total_commission_value < 100000000) {
                    $pipefyService->updateCardLabel($user, [PipefyService::LABEL_SALES_BETWEEN_100k_1M]);
                } elseif ($user->total_commission_value < 1000000000) {
                    $pipefyService->updateCardLabel($user, [PipefyService::LABEL_SALES_BETWEEN_1M_10M]);
                } elseif ($user->total_commission_value < 2500000000) {
                    $pipefyService->updateCardLabel($user, [PipefyService::LABEL_SALES_BETWEEN_10M_25M]);
                } elseif ($user->total_commission_value < 5000000000) {
                    $pipefyService->updateCardLabel($user, [PipefyService::LABEL_SALES_BETWEEN_25M_50M]);
                } elseif ($user->total_commission_value >= 5000000000) {
                    $pipefyService->updateCardLabel($user, [PipefyService::LABEL_SALES_OVER_50M]);
                }
            }
        }
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
            $pipefyService->moveCardToPhase($user, PipefyService::PHASE_ACTIVE_AND_SELLING);
        }
    }
}
