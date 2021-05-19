<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\Company;

class CompanyServiceBraspag
{
    private $braspagService;

    private $userService;

    public function __construct()
    {
        $this->braspagService = new BraspagBackOfficeService();
        $this->userService = new UserService();
    }

    public function createCompanyBraspag(Company $company)
    {
        try {
            $user = $company->user;

            if (($company->present()->getCompanyType($company->company_type) == 'physical person')
                /*&& (!$this->userService->verifyFieldsEmptyBraspag($user))*/
            ) {
                $result = $this->braspagService->createPfCompany($company);
            } elseif (($company->present()->getCompanyType($company->company_type) == 'juridical person')
                && !empty($user->cellphone) && !empty($user->email)) {
                $result = $this->braspagService->createPjCompany($company);
            } else {
                $this->updateBraspagStatusToError($company);
                throw new Exception("Dados incompletos {$company->fantasy_name} impossivel realizar cadastro");
            }

            if (empty($result) || empty(json_decode($result)->MerchantId) || empty(json_decode($result)->Analysis->Status)) {
                $this->updateBraspagStatusToError($company);
                throw new Exception("Erro ao cadastrar empresa {$company->fantasy_name} na braspag");
            }

            $this->updateBraspagStatusAndMerchantId(
                $company,
                json_decode($result)->MerchantId,
                $company->present()->getStatusBraspag(json_decode($result)->Analysis->Status)
            );
        } catch (Exception $e) {
            report($e);
        }
    }

//    private function updateBraspagStatusToError($company)
//    {
//        $company->update(
//            [
//                'braspag_status' => $company->present()->getStatusBraspag('Error'),
//            ]
//        );
//    }

    private function updateBraspagStatusAndMerchantId($company, $merchantId, $status)
    {
        if (FoxUtils::isProduction()) {
            $company->update(
                [
//                    '' => $merchantId,
//                    '' => $status ?? '',
                ]
            );
        } else {
            $company->update(
                [
//                    '' => $merchantId,
//                    '' => $status ?? '',
                ]
            );
        }
    }
}
