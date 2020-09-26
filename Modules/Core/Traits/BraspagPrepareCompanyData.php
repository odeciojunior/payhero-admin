<?php

namespace Modules\Core\Traits;

use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\UserInformation;
use Modules\Core\Services\FoxUtils;

/**
 * Trait BraspagPrepareDataTrait
 * @package Modules\Core\Traits
 */
trait BraspagPrepareCompanyData
{
    /**
     * @param Company $company
     * @return array
     * @throws PresenterException
     */
    public function getPrepareDataCreatePfCompany(Company $company)
    {
        $user = $company->user;
        $userInformation = $user->userInformation;

        return [
            'CorporateName' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'FancyName' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'DocumentNumber' => FoxUtils::onlyNumbers($company->company_document),
            'DocumentType' => 'CPF',
            'MerchantCategoryCode'=> '5719',
            'ContactName' => $user->name,
            'ContactPhone' => $company->present()->formatCellPhoneBraspag($user->cellphone),
            'MailAddress' => $user->email,
            'Website' => $company->business_website,
            'BankAccount' => [
                'Bank' => $company->bank,
                'BankAccountType' => $company->present()->getAccountType() == 'C' ? 'CheckingAccount' : 'SavingsAccount',
                'Number' => $company->account,
                // 'Operation' => '',
                'VerifierDigit' => empty($company->account_digit) ? 'x' : $company->account_digit,
                'AgencyNumber' =>$company->agency,
                'AgencyDigit' => empty($company->agency_digit) ? 'x' : $company->agency_digit,
                'DocumentNumber' => $company->company_document,
                'DocumentType' => 'CPF'
            ],
            'Address' => [
                'Street' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($user->street)),
                'Number' => $user->number ?? '',
                'Complement' => empty($user->complement) ? '' : FoxUtils::removeAccents(
                    FoxUtils::removeSpecialChars($user->complement)
                ),
                'Neighborhood' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($user->neighborhood)),
                'City' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($user->city)),
                'State' => $user->state,
                'ZipCode' => FoxUtils::onlyNumbers($user->zip_code)
            ],
            'Agreement' => [
                'Fee' => 100,
                'MerchantDiscountRates' =>  [
                    [
                        'PaymentArrangement' => [
                            'Product' => 'DebitCard',
                            'Brand' => 'Master'
                        ],
                        'InitialInstallmentNumber' => 1,
                        'FinalInstallmentNumber' => 1,
                        'Percent' => 6.5
                    ],
                ],
            ],
            'Notification' => [
                'Url' => 'https://app.cloudfox.net/postback/braspag',
                'Headers' => [
                    [
                        'Key' => 'cf-auth1',
                        'Value' => '729b6ae8730a46d9b63ee288e22ead44'
                    ],
                ],
            ],

        ];
    }



    /**
     * @param Company $company
     * @return array
     * @throws PresenterException
     */
    private function getPrepareDataCreatePjCompany(Company $company)
    {
        $user = $company->user;
        $userInformation = $user->userInformation;

        return [
            'CorporateName' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'FancyName' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'DocumentNumber' => $company->company_document,
            'DocumentType' => 'CNPJ',
            'MerchantCategoryCode'=> '5719',
            'ContactName' => $user->name,
            'ContactPhone' => $company->present()->formatCellPhoneBraspag($user->cellphone),
            'MailAddress' => $user->email,
            'Website' => $company->business_website,
            'BankAccount' => [
                'Bank' => $company->bank,
                'BankAccountType' => $company->present()->getAccountType() == 'C' ? 'CheckingAccount' : 'SavingsAccount',
                'Number' => $company->account,
                // 'Operation' => '', o que é operation ??
                'VerifierDigit' => $company->account_digit == 'X' || $company->account_digit == 'x' ? 0 : $company->account_digit,
                'AgencyNumber' =>$company->agency,
                'AgencyDigit' => FoxUtils::isEmpty($company->braspaagency_digitg_merchant_id) ? 'x' : $company->account_digit,
                'DocumentNumber' => $company->company_document,
                'DocumentType' => 'CNPJ'
            ],
            'Address' => [
                'Street' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->street)),
                'Number' => $company->number ?? '',
                'Complement' => empty($company->complement) ? '' : FoxUtils::removeAccents(
                    FoxUtils::removeSpecialChars($company->complement)
                ),
                'Neighborhood' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->neighborhood)),
                'City' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->city)),
                'State' => $company->state,
                'ZipCode' => FoxUtils::onlyNumbers($company->zip_code)
            ],
            'Agreement' => [
                'Fee' => 100,
                'MerchantDiscountRates' =>  [
                    [
                        'PaymentArrangement' => [
                            'Product' => 'DebitCard',
                            'Brand' => 'Master'
                        ],
                        'InitialInstallmentNumber' => 1,
                        'FinalInstallmentNumber' => 1,
                        'Percent' => 6.5
                    ],
                ],
            ],
            'Notification' => [
                'Url' => 'https://app.cloudfox.net/postback/braspag',
                'Headers' => [
                    [
                        'Key' => 'cf-auth1',
                        'Value' => '729b6ae8730a46d9b63ee288e22ead44'
                    ],
                ],
            ],

        ];
    }


}

