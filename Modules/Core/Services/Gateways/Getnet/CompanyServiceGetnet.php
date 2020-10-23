<?php


namespace Modules\Core\Services\Gateways\Getnet;


use Exception;
use Modules\Core\Entities\Company;
use Modules\Core\Services\GetnetBackOfficeService;

class CompanyServiceGetnet
{
    private Company $companyModel;
    private Company $company;

    public function __construct(Company $company)
    {
        $this->companyModel = new Company();
        $this->company = $company;
    }

    public function updateTaxCompanyGetnet($releaseMoneyDays)
    {
        try {
            $getnetService = new GetnetBackOfficeService();

            $paymentPlan = $getnetService->setTaxPlans($releaseMoneyDays['gateway_release_money_days']);
            if (is_null($paymentPlan)) {
                return false;
            }

            if ($this->companyModel->present()->getCompanyType($this->company->company_type) == 'physical person') {
                $listCommission = $getnetService->getListCommissions($releaseMoneyDays);
                $dataUpdated = array_merge($paymentPlan, ['list_commissions' => $listCommission]);
                $result = $getnetService->updatePfCompany($this->company, $dataUpdated);
            } else {
                $result = $getnetService->updatePjCompany($this->company, $paymentPlan);
            }

            if (!empty($result) && !empty(json_decode($result)->success) && json_decode($result)->success == true) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

}