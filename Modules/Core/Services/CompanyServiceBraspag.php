<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Modules\Companies\Transformers\CompanyResource;
use Modules\Core\Entities\AnticipatedTransaction;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Tracking;
use DB;

/**
 * Class CompaniesServiceBraspag
 * @package Modules\Core\Services
 */
class CompanyServiceBraspag
{

    public function verifyFieldsEmpty(Company $company)
    {
        if ($company->company_type == $company->present()->getCompanyType('juridical person')) {
            // informações basicas
            if (empty($company->zip_code)) {
                return true;
            }
            if (empty($company->street)) {
                return true;
            }
            if (empty($company->neighborhood)) {
                return true;
            }
            if (empty($company->state)) {
                return true;
            }
            if (empty($company->city)) {
                return true;
            }
            if (empty($company->country)) {
                return true;
            }
            // informações complementares
            if (empty($company->patrimony)) {
                return true;
            }
            if (empty($company->state_fiscal_document_number)) {
                return true;
            }
            if (empty($company->business_entity_type)) {
                return true;
            }
            if (empty($company->economic_activity_classification_code)) {
                return true;
            }
            if (empty($company->monthly_gross_income)) {
                return true;
            }
            if (empty($company->founding_date)) {
                return true;
            }
            if (empty($company->federal_registration_status_date)) {
                return true;
            }
            if (empty($company->social_value)) {
                return true;
            }
            if (empty($company->document_number)) {
                return true;
            }
            if (empty($company->document_issue_date)) {
                return true;
            }
            if (empty($company->document_issuer)) {
                return true;
            }
            if (empty($company->document_issuer_state)) {
                return true;
            }
        }

        if (empty($company->fantasy_name)) {
            return true;
        }
        if (empty($company->company_document)) {
            return true;
        }
        if (empty($company->bank)) {
            return true;
        }
        if (empty($company->agency)) {
            return true;
        }
        if (empty($company->account)) {
            return true;
        }
        return false;
    }

    /**
     * @param  Company  $company
     * @return string[]
     * @throws PresenterException
     */
    public function createCompanyBraspag(Company $company)
    {
        try {
            $braspagService = new BraspagBackOfficeService();
            //$braspagService = new BraspagService();
            $userService = new UserService();

            $user = $company->user;

            if (($company->present()->getCompanyType($company->company_type) == 'physical person')
                && (!$userService->verifyFieldsEmpty($user))
            ) {
                $result = $braspagService->createPfCompany($company);
            } elseif (($company->present()->getCompanyType($company->company_type) == 'juridical person')
                && !empty($user->cellphone) && !empty($user->email)) {
                $result = $braspagService->createPjCompany($company);
            }

            if (empty($result) || empty(json_decode(json_encode($result))->MerchantId)) {
                throw new Exception("Erro ao cadastrar empresa {$company->fantasy_name} na braspag");
            }


            if (FoxUtils::isProduction()) {
                $company->update(
                    [
                        'braspag_merchant_id' => json_decode(json_encode($result))->MerchantId,
                        'braspag_status' => $company->present()->getStatusBraspag(json_decode(json_encode($result))->Analysis->Status) ?? '',
                    ]
                );
            }else{
                $company->update(
                    [
                        'braspag_merchant_homolog_id' => json_decode(json_encode($result))->MerchantId,
                        'braspag_status' => $company->present()->getStatusBraspag(json_decode(json_encode($result))->Analysis->Status) ?? '',
                    ]
                );
            }


            return true;
        } catch (Exception $e) {
            report($e);

            return [
                'message' => 'error',
                'data' => '',
            ];
        }
    }


}
