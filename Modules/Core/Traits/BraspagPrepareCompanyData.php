<?php

namespace Modules\Core\Traits;

use Exception;
use Illuminate\Support\Facades\Storage;
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
            'DocumentNumber' => FoxUtils::getPortionOfString(FoxUtils::onlyNumbers($company->document), 0, 14),
            'MerchantCategoryCode' => '5719',
            'ContactName' => FoxUtils::getPortionOfString($user->name, 0, 100),
            'ContactPhone' => FoxUtils::formatCellPhoneBraspag($user->cellphone),
            'MailAddress' => FoxUtils::getPortionOfString($user->email, 0, 50),
            'Website' => null,
            'Address' => $address,
            'BankAccount' => $this->getBankData($company),
            'Agreement' => [
                "Fee" => 100,
//                "MdrPercentage" => $company->credit_card_tax,
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
            'Attachments' => [$this->getAttachments($company)]
        ];
    }

    private function getAttachments($company)
    {
        try {
            $document = CompanyDocument::where('company_id', $company->id)
                ->where('document_type_enum', 1)
                ->where('status', 3)
                ->latest()
                ->first();

            $documentUrl = explode('https://cloudfox.nyc3.digitaloceanspaces.com/', $document->document_url);

            $fileExists = Storage::disk('openSpaces')->exists($documentUrl[1]);

            if (!$fileExists) {
                $fileExists = Storage::disk('downloadSpaces')->exists($documentUrl[1]);

                if (!$fileExists) {
                    return [];
                }

                $typeFile = explode('.', $documentUrl[1]);
                $file = Storage::disk('downloadSpaces')->get($documentUrl[1]);
            } else {
                $typeFile = explode('.', $documentUrl[1]);
                $file = Storage::disk('openSpaces')->get($documentUrl[1]);
            }


            $imageBin = base64_encode($file);
            return [
                "AttachmentType" => "ProofOfBankDomicile",
                "File" => [
                    "Name" => "Comprovante",
                    "FileType" => $typeFile[1],
                    "Data" => $imageBin
                ]
            ];
        } catch (Exception $e) {
            return [];
        }
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
            'DocumentNumber' => FoxUtils::onlyNumbers($company->document),
            'DocumentType' => $company->company_type == 1 ? 'Cpf' : 'Cnpj',
        ];
    }

    private function getAddressCompanyBraspag($model)
    {
        return [
            'Street' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($model->street)),
            'Number' => $model->number ?? 'S/N',
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

