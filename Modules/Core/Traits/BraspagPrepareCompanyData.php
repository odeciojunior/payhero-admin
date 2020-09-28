<?php

namespace Modules\Core\Traits;

use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Services\FoxUtils;

trait BraspagPrepareCompanyData
{
    public function getPrepareDateCreateCompany(Company $company)
    {
        $user = $company->user;

        if ($company->company_type == $company->present()->getCompanyType('physical person')) {
            $address = $this->getAddressCompanyBraspag($user);
            $documentType = 'Cpf';
        } else {
            $address = $this->getAddressCompanyBraspag($company);
            $documentType = 'Cnpj';
        }

        return [
            'CorporateName' => FoxUtils::getPortionOfString(
                FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)), 0, 100),
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
            'BankAccount' => $this->getBankData($company),
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
            ],
            'Attachments' => $this->getAttachments($company)
        ];
    }

    private function getAttachments($company)
    {
        $document = CompanyDocument::where('company_id', $company->id)
            ->where('bank_document_status', 3)
            ->latest()
            ->fisrt();
        
        $imageBin = base64_encode($document->document_url);
        return [
            "AttachmentType" => "ProofOfBankDomicile",
            "File" => [
                "Name" => "comprovante",
                "FileType" => "png",
                "Data" => ''
            ]
        ];
    }

    private function getBankData($company)
    {
        switch ($company->agency_digit) {
            case 'x':
            case 'X':
            case '-':
            case '':
            case null:
                $agencyDigit = 'x';
                break;
            default:
                $agencyDigit = $company->agency_digit;
        }

        return [
            'Bank' => $company->bank,
            'BankAccountType' => $company->present()->getAccountType() == 'C' ? 'CheckingAccount' : 'SavingsAccount',
            'Number' => $company->account,
            'VerifierDigit' => empty($company->account_digit) ? 'x' : $company->account_digit,
            'AgencyNumber' => $company->agency,
            'AgencyDigit' => $agencyDigit,
            'DocumentNumber' => FoxUtils::onlyNumbers($company->company_document),
            'DocumentType' => $company->company_type == 1 ? 'Cpf' : 'Cnpj',
        ];
    }

    private function getAddressCompanyBraspag($model)
    {
        return [
            'Street' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($model->street)),
            'Number' => $model->number ?? '',
            'Complement' => empty($model->complement) ? '' : FoxUtils::removeAccents(
                FoxUtils::removeSpecialChars($model->complement)
            ),
            'Neighborhood' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($model->neighborhood)),
            'City' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($model->city)),
            'State' => FoxUtils::getFormatState($model->state),
            'ZipCode' => FoxUtils::onlyNumbers($model->zip_code)
        ];
    }
}

