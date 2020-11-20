<?php

namespace Modules\Core\Traits;

use Modules\Core\Entities\Company;
use Modules\Core\Services\FoxUtils;

trait GetnetPrepareCompanyData
{
    private string $urlCallback = 'https://app.cloudfox.net/postback/getnet';

    public function getPrepareDataCreatePfCompany(Company $company)
    {
        $user = $company->user;

        $telephone = FoxUtils::formatCellPhoneGetNet($user->cellphone);

        $motherName = $user->mother_name;

        if (empty($motherName) && !FoxUtils::isEmpty($user->id_wall_result)
            && !empty(json_decode($user->id_wall_result, true)['result']['pessoas_relacionadas'])
        ) {
            $idwall = json_decode($user->id_wall_result, true);
            $motherName = current(
                array_map(
                    function ($item) use ($user) {
                        if (isset($item['tipo']) && $item['tipo'] == 'MAE') {
                            return ($item['nome']);
                        }
                        return $user->mother_name;
                    },
                    $idwall['result']['pessoas_relacionadas']
                )
            );
        }

        return [
            'merchant_id' => $this->getMerchantId(),
            'legal_document_number' => FoxUtils::onlyNumbers($company->company_document),
            'legal_name' => FoxUtils::removeSpecialChars(FoxUtils::removeAccents($user->name)),
            'birth_date' => $user->date_birth,
            'mothers_name' => FoxUtils::removeSpecialChars(FoxUtils::removeAccents($motherName)),
            'occupation' => 'vendedor',
            'business_address' => [
                'mailing_address_equals' => 'S',
                'street' => FoxUtils::removeSpecialChars(FoxUtils::removeAccents($user->street)),
                'district' => FoxUtils::removeSpecialChars(FoxUtils::removeAccents($user->neighborhood)),
                'city' => FoxUtils::removeSpecialChars(FoxUtils::removeAccents($user->city)),
                'state' => FoxUtils::getFormatState($user->state),
                'number' => FoxUtils::onlyNumbers($user->number) ?? 0,
                'postal_code' => FoxUtils::onlyNumbers($user->zip_code),
            ],
            'working_hours' => [
                "start_day" => "mon",            // "mon" "tue" "wed" "thu" "fri" "sat" "sun"
                "end_day" => "fri",
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
            'list_commissions' => $this->getListCommissions($company),
            'url_callback' => $this->urlCallback,
            'accepted_contract' => 'S',
            'liability_chargeback' => 'S',
            'marketplace_store' => 'S',
            'payment_plan' => 3
        ];
    }

    public function getPrepareDataComplementPfCompany(Company $company)
    {

        return [];
    }

    public function getPrepareDataUpdatePfCompany(Company $company)
    {
        return [
            'merchant_id' => $this->getMerchantId(),
            'subseller_id' => FoxUtils::isProduction() ? $company->subseller_getnet_id : $company->subseller_getnet_homolog_id,
            'legal_document_number' => FoxUtils::onlyNumbers($company->company_document),
        ];
    }

    private function getPrepareDataCreatePjCompany(Company $company)
    {
        $user = $company->user;
        $telephone = FoxUtils::formatCellPhoneGetNet($user->cellphone);

        $stateFiscal = [
            'SERVIÇO É ISENTO',
            'SEM INSCRIÇÃO',
            'INSENTO',
            'n/e',
            'Não Possui',
            'Nao possui',
            'nao possui',
            'não possui',
            'não se aplica',
            'nao tem',
            'Não tem',
            'não',
            '00000000',
            '000000000000',
            '000',
            'ISENTO',
            'isento',
            'Isento',
            'Insento',
            'Isenta',
            'Não Contribuinte'
        ];

        if (empty($company->state_fiscal_document_number) || strlen($company->state_fiscal_document_number) < 4
            || in_array($company->state_fiscal_document_number, $stateFiscal)
        ) {
            $stateFiscalNumber = 'ISENTO';
        } else {
            $stateFiscalNumber = FoxUtils::onlyNumbers($company->state_fiscal_document_number);
        }

        return [
            'merchant_id' => $this->getMerchantId(),
            'legal_document_number' => FoxUtils::onlyNumbers($company->company_document),
            'legal_name' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'trade_name' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'state_fiscal_document_number' => $stateFiscalNumber,
            'email' => $user->email,
            'cellphone' => [
                'area_code' => $telephone['dd'],
                'phone_number' => $telephone['number']
            ],
            'business_address' => [
                'street' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->street)),
                'number' => $company->number == null ? '' : $company->number,
                'district' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->neighborhood)),
                'city' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->city)),
                'state' => FoxUtils::getFormatState($company->state),
                'postal_code' => FoxUtils::onlyNumbers($company->zip_code),
                'country' => 'BR'
            ],
            'bank_accounts' => [
                'type_accounts' => 'unique',
                'unique_account' => [
                    'bank' => $company->bank,
                    'agency' => $company->agency,
                    'account' => $company->account,
                    'account_type' => $company->present()->getAccountType(),
                    'account_digit' => $company->account_digit == 'X' || $company->account_digit == 'x' ? 0 : $company->account_digit,
                ]
            ],
            'url_callback' => $this->urlCallback,
            "accepted_contract" => "S",
            "liability_chargeback" => "S",
            'marketplace_store' => "S",
            'payment_plan' => 3,
            'business_entity_type' => FoxUtils::onlyNumbers($company->business_entity_type),
            'economic_activity_classification_code' => FoxUtils::onlyNumbers(
                $company->economic_activity_classification_code
            ),
            'federal_registration_status' => 'active',
            'founding_date' => $company->founding_date,
        ];
    }

    private function getPrepareDataComplementPjCompany(Company $company)
    {
        return [
            'merchant_id' => $this->getMerchantId(),
            'subseller_id' => $company->subseller_getnet_id,
            'legal_document_number' => $company->company_document,
            "working_hours" => [
                "start_day" => "mon",
                "end_day" => "mon",
                "start_time" => "08:00:00",
                "end_time" => "18:00:00"
            ],
            'identification_document' => [
                'document_type' => 'nire',
                'document_number' => '',
                'document_issue_date' => $company->document_issue_date,
                'document_issuer' => $company->document_issuer,
                'document_issuer_state' => $company->document_issuer_state
            ],
            'federal_registration_status_date' => $company->federal_registration_status_date,
            'social_value' => $company->social_value,
        ];
    }

    private function getPrepareDataUpdatePjCompany(Company $company)
    {
        return [
            'merchant_id' => $this->getMerchantId(),
            'subseller_id' => FoxUtils::isProduction() ? $company->subseller_getnet_id : $company->subseller_getnet_homolog_id,
            'legal_document_number' => $company->company_document,
        ];
    }

    public function getListCommissions($gatewayData)
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
                        'commission_percentage' => 100 - $gatewayData['gateway_tax'],
                        'payment_plan' => $this->getPlans($gatewayData['gateway_release_money_days']),
                    ];
                }
            }
        }

        return $listCommissions;
    }

    public function setTaxPlans($releaseMoneyDays)
    {
        $paymentPlanId = $this->getPlans($releaseMoneyDays);

        if (in_array($paymentPlanId, [3, 5, 8])) {
            return ['payment_plan' => $paymentPlanId];
        }

        return null;
    }

    private function getPlans($releaseMoneyDays)
    {
        $plans = [
            'plans' => [
                'days' => [
                    2 => 3,
                    15 => 8,
                    30 => 5
                ],
            ]
        ];


        return $plans['plans']['days'][$releaseMoneyDays];
    }
}

