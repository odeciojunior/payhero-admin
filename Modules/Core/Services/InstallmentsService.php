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

        $userProjectModel = new UserProject();

        $userModel = new User();

        $userProject = $userProjectModel->where('project_id', $project->id)->where('type_enum', $userProjectModel->present()->getTypeEnum('producer'))->first();

        $user = $userModel->find($userProject->user_id);

        $installmentValueTax = intval($totalValue / 100 * $user->installment_tax);

        $totalValue = preg_replace("/[^0-9]/", "", $totalValue);

        $installmentsData = array();

        for ($installmentAmount = 1; $installmentAmount <= $project->installments_amount; $installmentAmount++) {

            $installmentData = array();

            if ($installmentAmount == 1) {
                $installmentData['amount'] = $installmentAmount;
                $installmentData['value'] = number_format(intval($totalValue) / 100, 2, ',', '.');
                $installmentData['total_value'] = number_format(intval($totalValue) / 100, 2, ',', '.');
            } else {
                if ($project->installments_interest_free >= $installmentAmount) {
                    $totalValueWithTax = $totalValue;
                } else {
                    $totalValueWithTax = $totalValue + $installmentValueTax * ($installmentAmount - 1);
                }

                $installmentValue = intval($totalValueWithTax / $installmentAmount);

                if ($installmentValue < 500) {
                    continue;
                }

                $installmentData['amount'] = $installmentAmount;
                $installmentData['value'] = number_format($installmentValue / 100, 2, ',', '.');
                $installmentData['total_value'] = number_format(intval($totalValueWithTax) / 100, 2, ',', '.');
            }

            $installmentsData[] = $installmentData;
        }

        return $installmentsData;
    }
}
