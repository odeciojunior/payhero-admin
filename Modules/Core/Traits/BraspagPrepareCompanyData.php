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

        $telephone = FoxUtils::formatCellPhoneGetNet($user->cellphone);

        $country = 'BR';
        if ($company->country == 'usa') {
            $country = 'EUA';
        }

        return [
            'merchant_id' => $this->getMerchantId(),
            'legal_document_number' => $company->company_document,
            'legal_name' => FoxUtils::removeSpecialChars(FoxUtils::removeAccents($user->name)),
            'birth_date' => $user->date_birth,
            'mothers_name' => FoxUtils::removeSpecialChars(FoxUtils::removeAccents($userInformation->mother_name)),
            'occupation' => 'vendedor',
            'monthly_gross_income' => FoxUtils::onlyNumbers($userInformation->monthly_income),
            'business_address' => [
                'mailing_address_equals' => 'S',
                'street' => FoxUtils::removeSpecialChars(FoxUtils::removeAccents($user->street)),
                'number' => $user->number ?? '',
                'district' => FoxUtils::removeSpecialChars(FoxUtils::removeAccents($user->neighborhood)),
                'city' => FoxUtils::removeSpecialChars(FoxUtils::removeAccents($user->city)),
                'state' => $user->state,
                'postal_code' => FoxUtils::onlyNumbers($user->zip_code),
                'suite' => empty($user->complement) ? '' : FoxUtils::removeAccents(
                    FoxUtils::removeSpecialChars($user->complement)
                ),
                'country' => $country
            ],
            'working_hours' => [
                "start_day" => "mon",            // "mon" "tue" "wed" "thu" "fri" "sat" "sun"
                "end_day" => "mon",
                "start_time" => "08:00:00",      // "hh:mm:ss"
                "end_time" => "18:00:00"
            ],
            'cellphone' => [
                'area_code' => $telephone['dd'],
                'phone_number' => $telephone['number'],
            ],
            'email' => $user->email,
            'acquirer_merchant_category_code' => '2119',
            'bank_accounts' => [
                'type_accounts' => 'unique',
                'unique_account' => [
                    'bank' => $company->bank,
                    'agency' => $company->agency,
                    'account' => $company->account,
                    'account_type' => $company->present()->getAccountType(),
                    'account_digit' => $company->account_digit,
                ]
            ],
            "list_commissions" => [
                [
                    "brand" => "MASTERCARD",
                    "product" => "CREDITO A VISTA",
                    "commission_percentage" => 10.00,
                    "payment_plan" => 3
                ]
            ],
            'url_callback' => 'https://app.cloudfox.net/postback/getnet',
            'accepted_contract' => 'S',
            'liability_chargeback' => 'S',
            'marketplace_store' => 'S',
            'payment_plan' => 3
        ];
    }

    /**
     * @param Company $company
     * @return array
     */
    public function getPrepareDataComplementPfCompany(Company $company)
    {
        $user = $company->user;
        $userInformation = $user->userInformation;

        return [
            'merchant_id' => $this->getMerchantId(),
            'subseller_id' => $company->subseller_getnet_id,
            'legal_document_number' => $company->company_document,
            'identification_document' => [
                'document_type' => 'id_card', //
                'document_number' => $userInformation->document_number,
                'document_issue_date' => $userInformation->document_issue_date,
                'document_expiration_date' => $userInformation->document_expiration_date,
                'document_issuer' => $userInformation->document_issuer,
                'document_issuer_state' => $userInformation->document_issuer_state,
            ],
            'sex' => $userInformation->sex,
            'marital_status' => $userInformation->marital_status,
            'nationality' => $userInformation->nationality,
            'fathers_name' => $userInformation->father_name,
            'spouse_name' => $userInformation->spouse_name,
            'birth_place' => $userInformation->birth_place,
            'birth_city' => $userInformation->birth_city,
            'birth_state' => $userInformation->birth_state,
            'birth_country' => $userInformation->birth_country,
            'ppe_indication' => 'not_applied',
            'patrimony' => $company->patrimony
        ];
    }

    /**
     * @param Company $company
     * @return array
     * @throws PresenterException
     */
    public function getPrepareDataUpdatePfCompany(Company $company)
    {
        $user = $company->user;
        $userInformation = $user->userInformation;
        return [
            'merchant_id' => $this->getMerchantId(),
            'subseller_id' => $company->subseller_getnet_id,
            'legal_document_number' => FoxUtils::onlyNumbers($company->company_document),
            'email' => $user->email,
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
        $telephone = FoxUtils::formatCellPhoneBraspag($user->cellphone);

        return [
            'CorporateName' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'FancyName' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'DocumentNumber' => $company->company_document,
            'DocumentType' => 'CNPJ',
            'MerchantCategoryCode'=> '5719',
            'ContactName' => $user->name,
            'ContactPhone' => $user->cellphone,
            'MailAddress' => $user->email,
            'Website' => $company->business_website,
            'BankAccount' => [
                'Bank' => $company->bank,
                'BankAccountType' => $company->present()->getAccountType() == 'C' ? 'CheckingAccount' : 'SavingsAccount',
                'Number' => $company->account,
                'Operation' => '',
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
            'Agreement' =>  $this->getAgreementListCommissions($company->user),



            // "Notification": {
            //     "Url": "https://site.com.br/api/subordinados",
            //     "Headers": [{
            //         "Key": "key1",
            //         "Value": "value1"
            //     },
            //     {
            //         "Key": "key2",
            //         "Value": "value2"
            //     }]
            // },
            'Notification' => [
                'Url' => 'https://app.cloudfox.net/postback/braspag',
                'Headers' => [
                    [
                        'Key' => 'cf-auth1',
                        'Value' => '729b6ae8730a46d9b63ee288e22ead44'
                    ],

                ]

            ]

            // "Attachments": [{
            //     "AttachmentType": "ProofOfBankDomicile",
            //     "File": {
            //         "Name": "comprovante",
            //         "FileType": "png",
            //         "Data": "iVBORw0KGgoAAAANSUhEUgAAAH4AAAAsCAMAAACUu/xGAAAAq1BMVEUAAABlZVJlZVKsrJthYU+zs6Grq5ylpZazs6FlZVJfX01lZVJlZVKsrJurq5urq5xlZVKtrZ1lZVJlZVKvr52zs6GysqCoqJeqqpmzs6Grq5xlZVJgYE6zs6Gnp5mrq5yiopRjY1CRkX2rq5yzs6FlZVKRkX2goJKKineRkX2Pj3yrq5yIiHWRkX2RkX2RkX1lZVKRkX2rq5yzs6GoqJdfX02goJKHh3SHh3VrpzVsAAAAMHRSTlMAQIDHx3+Ax0Ag7qBgIA9AEFCPMLOgMO7bYKBQ24+zYNuzkY9wcAXu0oiocPFBMHYlVbK0AAAD3UlEQVRYw6SW7Y6qMBCGB0IkLfKdnB9ocFmjru7HERL03P+VnXY6bdmWjcF9f2inxjydvjMDcHy99zP693oEpTpQYjBR7W4VmzA81GoZCDn/ycrValVmYOJcKBWL1/4HnUEpupLGxOI47iQmDkfc4GEBEFyNQkClzYDKQQs3VmJBufu6G7zRWNMeUzEHUnLVWs/gy9vg4NNB4wUIPOG2h7e8NcV0HRt7QPDxfzTd4ptleB5F6ro3NtsIc7UnjMKKXyuN30ZS+PuLRMW7PN+l2vlhAZ6yqCZmcrm05stfOrwVpvEBaJWStIOpVk/gC8Rb62tjRj25Fx/fEsgqE27cluKB8GR9hDFzeX44CFbmJb9/Cn8w1ldA5tO9VD/gc8FpveTbxfi1LXWOl10Z80c0Yx7/jpyyjRtd9zuxU8ZL8FEYJjZFpg6yIfOpKsf1FJ+EUkzddKkabQ+o0zCcwMN/vZm+uLh4UmW7nptTCBVq5nUF4Y0CgBaNVip18jsPn370909cfX708/gusF3fkQfrKZHXHh45Wi8meRefvfVCfwGOZ9zx8TZ9TjWY2M6vVf4jm8e3WYrDJ1Vj4N3FHwVd6vKFCxefBMFmq7ub6UI7TMZw0SEv8ryPDVaoxPiWufhL/02zY0cm3ZH1VgxIIYa1U/nIibH/EZjjp4M/9w/x9FijbyuqdzOVH+BbWQJxHMupd4pjINhDPKVH1lslBl9g6OKb73j0wmoBHrMj691nsJ0QLn4l0/09nrIm6wv7nGdQqwjGucvPJSWjN4z8aXyBlkfK+i2gmDI/HENGjXA9uPhsUJ22p2OQFg3daaFx0/9qnWBRbOl9hHlvOw3OW/xs4Hf4rcnYzj+OeFOIHj4dtG7/2y+b3IhBGAqjUiQWQ9JI/ErDpop6gcei9z9ZIXHIhLaLSGRW8zYxIuaTZccxqsGfHDXvH4cf37Z4e3ihxVOTp5bf4E8N2u+3PWB2SP7tXsfsFl80rtOeZX/gvz6//7tmnFFzD2mkxnFgL710ToHH1eCcm/LU2aA9m027v+kBH8ipyHbACxAMWaV5I4v2ZgAzIxkUGXIqkn3xrhw4wVe8hoMmOwBmYJMiJy+lHPriNcSyrvgEgUS2h/vl1BcvSqgcZsPbbABrhgdgvhgvS6hIYsPP8MwTVR5SLZA4573xHMpCV7xGZBFmxyProfR64yNCgKh4hygjXIuvpdcbPyEayA2vsEpRHcgl6gtzr8A9ho0RlgQnBPoK4tV45gBfGQZ6KQBDqzRcjdeAqQwHUfYp+SohcQdc1/Ukm4Gw4dV6vqTkM+uQpRv8E2VPF/sPp9xSb2qlGH4AAAAASUVORK5CYII="
            //     }
            // }]

            'Attachments' => [
                [
                    'AttachmentType' =>
                ]
        ];


    }

    private function getAgreementListCommissions($user)
    {
        $listCommissions = [];

        $brands = [
            "MASTERCARD",
            "MAESTRO",
            "VISA",
            "VISA ELECTRON",
            "AMEX",
            "ELO CRÉDITO",
            "ELO DÉBITO",
            "HIPERCARD"
        ];

        $products = [
            "DEBITO A VISTA",
            "CREDITO A VISTA",
            "PARCELADO LOJISTA 3X",
            "PARCELADO LOJISTA 6X",
            "PARCELADO LOJISTA 9X",
            "PARCELADO LOJISTA 12X",
            "PARCELADO EMISSOR",
            "BOLETO"
        ];

        foreach ($brands as $brand) {
            foreach ($products as $product) {
                if (!in_array([$product, $brand], $listCommissions)) {
                    switch ($product) {
                        case 'BOLETO':
                            $value = 100 - $user->boleto_tax;
                            break;
                        case 'DEBITO A VISTA':
                            $value = 100 - $user->debit_card_tax;
                            break;
                        default:
                            $value = 100 - $user->credit_card_tax;
                    }

                    if ((in_array($brand, $brands)) && ($product == 'DEBITO A VISTA' || $product == 'BOLETO')) {
                        continue;
                    }

                    if ((in_array($brand, ['MAESTRO', 'VISA ELECTRON', 'ELO DÉBITO']))
                        && in_array($product, [
                            'CREDITO A VISTA',
                            'PARCELADO LOJISTA 3X',
                            'PARCELADO LOJISTA 6X',
                            'PARCELADO LOJISTA 9X',
                            'PARCELADO LOJISTA 12X',
                            'PARCELADO EMISSOR'
                        ])) {
                        continue;
                    }

                    $listCommissions[] = [
                        'brand' => $brand,
                        'product' => $product,
                        'commission_percentage' => $value,
                        'payment_plan' => 3,
                    ];
                }
            }
        }

        return $listCommissions;
    }
}

