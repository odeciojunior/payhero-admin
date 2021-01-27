<?php

declare(strict_types=1);

namespace Modules\Core\Observers;

use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;

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

        if ($user->address_document_status == $user->present()->getAddressDocumentStatus('approved') &&
            $user->personal_document_status == $user->present()->getPersonalDocumentStatus('approved'))
        {
            $hasCompanyPfApproved = Company::where('user_id', $user->id)
                                           ->where('company_type', 1)
                                           ->where('bank_document_status', 3)
                                           ->where('capture_transaction_enabled', 1)
                                           ->exists();

            $hasCompanyPjApproved = Company::where('user_id', $user->id)
                                           ->where('company_type', 2)
                                           ->where('address_document_status', 3)
                                           ->where('contract_document_status', 3)
                                           ->where('bank_document_status', 3)
                                           ->where('capture_transaction_enabled', 1)
                                           ->exists();

            if ($hasCompanyPjApproved || $hasCompanyPfApproved)
                DB::table('users')->where('id', $user->id)->update(['account_is_approved' => true]);
        }
    }
}
