<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Services\Pipefy\PipefyService;

class PipefyUpdateCard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pipefy:update-card';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pipefy Update Card';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->updateCardInformation();
        $this->checkDocumentUser();
        $this->checkFirstSale();
        $this->updateCardlabel();
    }

    public function checkDocumentUser()
    {
        $pipefyService = new PipefyService();

        $usersDocumentRefused = User::selectRaw("users.*")
            ->selectRaw("(
                        SELECT count( c.id ) FROM companies AS c
                            WHERE c.user_id = users.id
                                AND (c.address_document_status = 3 OR c.contract_document_status = 3 )
                            GROUP BY c.user_id
                        ) total_companies")
            ->whereNotNull("users.pipefy_card_id")
            ->where(function ($q) {
                $q->where("address_document_status", UserDocument::STATUS_REFUSED);
                $q->orWhere("personal_document_status", UserDocument::STATUS_REFUSED);
            })
            ->get();

        foreach ($usersDocumentRefused as $user) {
            if (empty($user->total_companies)){
                $pipefyService->moveCardToPhase($user, PipefyService::PHASE_REFUSED_DOCUMENT);
            }
        }

        $usersApproved = User::whereNotNull("users.pipefy_card_id")->where("users.account_is_approved","=",1)->get();

        foreach ($usersApproved as $user) {
            $pipefyService->moveCardToPhase($user, PipefyService::PHASE_ACTIVE);
        }

    }

    public function checkFirstSale()
    {
        $pipefyService = new PipefyService();
        $labelArray = [PipefyService::LABEL_SOLD];

        $users = User::select("users.*")->join("tasks_users as t", "users.id", "t.user_id")
            ->whereNotNull("users.pipefy_card_id")
            ->where("task_id", Task::TASK_FIRST_SALE)
            ->get();

        foreach ($users as $user) {
            $pipefyService->moveCardToPhase($user, PipefyService::PHASE_ACTIVE_AND_SELLING);
        }
    }

    public function updateCardInformation()
    {
        $pipefyService = new PipefyService();

        $users = User::whereNotNull("users.pipefy_card_id")->get();

        foreach ($users as $user) {
            $pipefyService->updateCardUserinformations($user);
        }
    }

    public function updateCardlabel()
    {
        $pipefyService = new PipefyService();

        $users = User::whereNotNull("users.pipefy_card_id")->get();

        foreach ($users as $user) {
            if ($user->total_commission_value < 10000000){
                $pipefyService->updateCardLabel($user, [PipefyService::LABEL_SALES_BETWEEN_0_100k]);
            }elseif ($user->total_commission_value < 100000000 ){
                $pipefyService->updateCardLabel($user, [PipefyService::LABEL_SALES_BETWEEN_100k_1M]);
            }elseif ($user->total_commission_value < 1000000000){
                $pipefyService->updateCardLabel($user, [PipefyService::LABEL_SALES_BETWEEN_1M_10M]);
            }elseif ($user->total_commission_value < 2500000000){
                $pipefyService->updateCardLabel($user, [PipefyService::LABEL_SALES_BETWEEN_10M_25M]);
            }elseif ($user->total_commission_value >= 2500000000){
                $pipefyService->updateCardLabel($user, [PipefyService::LABEL_SALES_BETWEEN_25M_50M]);
            }
        }
    }

}
