<?php

namespace Modules\Core\Services;

use App\Jobs\PipefyMoveCardPhaseJob;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Services\Pipefy\PipefyService;

/**
 * Class AccountApprovedService
 * @package Modules\Core\Services
 */
class AccountApprovedService
{
    /**
     * @param $user
     * @throws Exception
     */
    public function checkAccountIsApproved(User $user)
    {
        if ($user->account_is_approved) {
            return;
        }

        if (
            $user->address_document_status == UserDocument::STATUS_APPROVED &&
            $user->personal_document_status == UserDocument::STATUS_APPROVED
        ) {
            $hasCompanyPfApproved = Company::where("user_id", $user->id)
                ->where("company_type", Company::PHYSICAL_PERSON)
                ->exists();

            $hasCompanyPjApproved = Company::where("user_id", $user->id)
                ->where("company_type", Company::JURIDICAL_PERSON)
                ->where(
                    "address_document_status",
                    CompanyDocument::STATUS_APPROVED
                )
                ->where(
                    "contract_document_status",
                    CompanyDocument::STATUS_APPROVED
                )
                ->exists();

            if ($hasCompanyPjApproved || $hasCompanyPfApproved) {
                DB::table("users")
                    ->where("id", $user->id)
                    ->update(["account_is_approved" => true]);

                if (FoxUtils::isProduction()) {
                    // PipefyMoveCardPhaseJob::dispatch(
                    //     $user,
                    //     PipefyService::PHASE_ACTIVE
                    // );
                }
            }

            if (FoxUtils::isProduction()) {
                // PipefyMoveCardPhaseJob::dispatch(
                //     $user,
                //     PipefyService::PHASE_ACTIVE
                // );
            }
        }
    }
}
