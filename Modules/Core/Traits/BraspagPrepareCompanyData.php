<?php

namespace Modules\Core\Traits;

use Modules\Core\Entities\Company;
use Modules\Core\Services\FoxUtils;

trait BraspagPrepareCompanyData
{
    private function getPrepareDateCreateCompany(Company $company)
    {
        $user = $company->user;

        if ($company->company_type == $company->present()->getCompanyType('physical person')) {
            $address = $this->getAddressPfCompanyBraspag($user);
            $documentType = 'Cpf';
        } else {
            $address = $this->getAddressPjCompanyBraspag($company);
            $documentType = 'Cnpj';
        }


        return [
            'CorporateName' => FoxUtils::getPortionOfString(
                FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)), 0,100),
            'FancyName' => FoxUtils::getPortionOfString(
                FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)), 0, 50),
            'DocumentType' => $documentType,
            'DocumentNumber' => FoxUtils::getPortionOfString(FoxUtils::onlyNumbers($company->company_document), 0, 14),
            'MerchantCategoryCode' => '5719',
            'ContactName' => FoxUtils::getPortionOfString($user->name, 0, 100),
            'ContactPhone' => FoxUtils::formatCellPhoneBraspag($user->cellphone),
            'MailAddress' => FoxUtils::getPortionOfString($user->email, 0, 50),
            'Website' => $company->business_website ?? null,
            'Address' => $address,
            'BankAccount' => [
                'Bank' => $company->bank,
                'BankAccountType' => $company->present()->getAccountType() == 'C' ? 'CheckingAccount' : 'SavingsAccount',
                'Number' => $company->account,
                'VerifierDigit' => empty($company->account_digit) ? 'x' : $company->account_digit,
                'AgencyNumber' => $company->agency,
                'AgencyDigit' => empty($company->agency_digit) ? 'x' : $company->agency_digit,
                'DocumentNumber' => $company->company_document,
                'DocumentType' => $company->company_type == 1 ? 'Cpf' : 'Cnpj',
            ],
            'Agreement' => [
                'Fee' => 100,
                'MerchantDiscountRates' => [
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
            ]
        ];
    }

    private function getAddressPfCompanyBraspag($user){
        return [
            'Street' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($user->street)),
            'Number' => $user->number ?? '',
            'Complement' => empty($user->complement) ? '' : FoxUtils::removeAccents(
                FoxUtils::removeSpecialChars($user->complement)
            ),
            'Neighborhood' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($user->neighborhood)),
            'City' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($user->city)),
            'State' => $user->state,
            'ZipCode' => FoxUtils::onlyNumbers($user->zip_code)
        ];
    }

    private function getAddressPjCompanyBraspag($company){
        return [
            'Street' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->street)),
            'Number' => $company->number ?? '',
            'Complement' => empty($company->complement) ? '' : FoxUtils::removeAccents(
                FoxUtils::removeSpecialChars($company->complement)
            ),
            'Neighborhood' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->neighborhood)),
            'City' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->city)),
            'State' => $company->state,
            'ZipCode' => FoxUtils::onlyNumbers($company->zip_code)
        ];
    }
}

