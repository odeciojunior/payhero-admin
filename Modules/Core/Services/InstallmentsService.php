<?php

namespace Modules\Core\Services;

use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserProject;

class InstallmentsService
{
    /**
     * @param $project
     * @param $totalValue
     * @return array
     * @throws PresenterException
     */
    public static function getInstallments(Project $project, $totalValue)
    {
        $project->loadMissing("checkoutConfig.company");
        $checkoutConfig = $project->checkoutConfig;

        $installmentValueTax = intval(($totalValue / 100) * $checkoutConfig->company->installment_tax);

        $totalValue = preg_replace("/[^0-9]/", "", $totalValue);

        $installmentsData = [];

        for ($installmentAmount = 1; $installmentAmount <= $checkoutConfig->installments_limit; $installmentAmount++) {
            $installmentData = [];

            if ($installmentAmount == 1) {
                $installmentData["amount"] = $installmentAmount;
                $installmentData["value"] = number_format(intval($totalValue) / 100, 2, ",", ".");
                $installmentData["total_value"] = number_format(intval($totalValue) / 100, 2, ",", ".");
            } else {
                if ($checkoutConfig->interest_free_installments >= $installmentAmount) {
                    $totalValueWithTax = $totalValue;
                } else {
                    $totalValueWithTax = $totalValue + $installmentValueTax * ($installmentAmount - 1);
                }

                $installmentValue = intval($totalValueWithTax / $installmentAmount);

                if ($installmentValue < 500) {
                    continue;
                }

                $installmentData["amount"] = $installmentAmount;
                $installmentData["value"] = number_format($installmentValue / 100, 2, ",", ".");
                $installmentData["total_value"] = number_format(intval($totalValueWithTax) / 100, 2, ",", ".");
            }

            $installmentsData[] = $installmentData;
        }

        return $installmentsData;
    }
}
