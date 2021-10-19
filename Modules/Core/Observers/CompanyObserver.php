<?php

declare(strict_types=1);

namespace Modules\Core\Observers;

use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Entities\UserDocument;

/**
 * Class CompanyObserver
 * @package App\Observers
 */
class CompanyObserver
{
    /**
     * Handle the user "updated" event.
     *
     * @param  Company  $company
     * @return void
     */
    public function updated(Company $company)
    {
        $user = $company->user;

        if ($user->address_document_status == UserDocument::STATUS_APPROVED &&
            $user->personal_document_status == UserDocument::STATUS_APPROVED) {
            $hasCompanyPfApproved = Company::whereHas('gatewayCompanyCredential', function($q) {
                                                $q->where('gateway_id', Gateway::GETNET_PRODUCTION_ID);
                                                $q->where('gateway_status', GatewaysCompaniesCredential::GATEWAY_STATUS_APPROVED);
                                                $q->where('capture_transaction_enabled', 1);
                                            })
                                            ->where('user_id', $user->id)
                                            ->where('company_type', Company::COMPANY_TYPE_PHYSICAL_PERSON)
                                            ->where('bank_document_status', Company::STATUS_APPROVED)
                                            ->exists();

            $hasCompanyPjApproved = Company::whereHas('gatewayCompanyCredential', function($q) {
                                                $q->where('gateway_id', Gateway::GETNET_PRODUCTION_ID);
                                                $q->where('gateway_status', GatewaysCompaniesCredential::GATEWAY_STATUS_APPROVED);
                                                $q->where('capture_transaction_enabled', 1);
                                            })
                                            ->where('user_id', $user->id)
                                            ->where('company_type', Company::COMPANY_TYPE_JURIDICAL_PERSON)
                                            ->where('address_document_status', CompanyDocument::STATUS_APPROVED)
                                            ->where('contract_document_status', CompanyDocument::STATUS_APPROVED)
                                            ->where('bank_document_status', CompanyDocument::STATUS_APPROVED)
                                            ->exists();

            if ($hasCompanyPjApproved || $hasCompanyPfApproved)
                DB::table('users')->where('id', $user->id)->update(['account_is_approved' => true]);
        }
    }
}
