<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserDocument;

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

        if ($user->account_is_approved) return;

        if ($user->address_document_status == UserDocument::STATUS_APPROVED &&
            $user->personal_document_status == UserDocument::STATUS_APPROVED) {
            $hasCompanyPfApproved = Company::whereHas('gatewayCompanyCredential', function($q) {
                    $q->where('gateway_id', Gateway::GETNET_PRODUCTION_ID);
                    $q->where('gateway_status', GatewaysCompaniesCredential::GATEWAY_STATUS_APPROVED);
                    $q->where('capture_transaction_enabled', 1);
                })
                ->where('user_id', $user->id)
                ->where('company_type', Company::PHYSICAL_PERSON)
                ->where('bank_document_status', Company::STATUS_APPROVED)
                ->exists();

            $hasCompanyPjApproved = Company::whereHas('gatewayCompanyCredential', function($q) {
                    $q->where('gateway_id', Gateway::GETNET_PRODUCTION_ID);
                    $q->where('gateway_status', GatewaysCompaniesCredential::GATEWAY_STATUS_APPROVED);
                    $q->where('capture_transaction_enabled', 1);
                })
                ->where('user_id', $user->id)
                ->where('company_type', Company::JURIDICAL_PERSON)
                ->where('address_document_status', CompanyDocument::STATUS_APPROVED)
                ->where('contract_document_status', CompanyDocument::STATUS_APPROVED)
                ->where('bank_document_status', CompanyDocument::STATUS_APPROVED)
                ->exists();

            if ($hasCompanyPjApproved || $hasCompanyPfApproved) {
                DB::table('users')->where('id', $user->id)->update(['account_is_approved' => true]);
            }
        }

    }
}
