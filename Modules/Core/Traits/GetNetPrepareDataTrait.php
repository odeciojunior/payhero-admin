<?php

namespace Modules\Core\Traits;

use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Company;
use Modules\Core\Services\FoxUtils;

/**
 * Trait GetNetPrepareDataTrait
 * @package Modules\Core\Traits
 */
trait GetNetPrepareDataTrait
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
            // @todo AINDA VERIFICAR SE ESTA CORRETO / PF É OBRIGATORIO
            "list_commissions" => [
                [
                    "brand" => "MASTERCARD",
                    "product" => "CREDITO A VISTA",
                    "commission_percentage" => 10.00,
                    "payment_plan" => 3
                ]
            ],
            'url_callback' => $this->urlCallback,
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
        $userInformationPresent = $userInformation->present();

        return [
            'merchant_id' => $this->getMerchantId(),
            'subseller_id' => $company->subseller_getnet_id,
            'legal_document_number' => $company->company_document,
            'identification_document' => [
                'document_type' => $userInformationPresent->getDocumentType(), //
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
            'legal_name' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($user->name)),
            'birth_date' => $user->date_birth,
            'mothers_name' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($userInformation->mother_name)),
            'monthly_gross_income' => FoxUtils::onlyNumbers($company->monthly_gross_income),
            'business_address' => [
                'street' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($user->street)),
                'number' => FoxUtils::onlyNumbers($user->number),
                'district' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($user->neighborhood)),
                'city' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($user->city)),
                'state' => $user->state,
                'postal_code' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($user->zip_code)),
                'country' => 'BR',
            ],
            'email' => $user->email,
            'bank_accounts' => [
                'type_accounts' => 'unique',
                'unique_account' => [
                    'bank' => FoxUtils::onlyNumbers($company->bank),
                    'agency' => FoxUtils::onlyNumbers($company->agency),
                    'account' => FoxUtils::onlyNumbers($company->account),
                    'account_type' => $company->present()->getAccountType(),
                    'account_digit' => FoxUtils::onlyNumbers($company->account_digit),
                ]
            ]
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
        $telephone = FoxUtils::formatCellPhoneGetNet($user->cellphone);
        return [
            'merchant_id' => $this->getMerchantId(),
            'legal_document_number' => $company->company_document,
            'legal_name' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'trade_name' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'state_fiscal_document_number' => empty($company->state_fiscal_document_number) ? 'ISENTO' : $company->state_fiscal_document_number,
            'email' => $user->email,
            'cellphone' => [
                'area_code' => $telephone['dd'],
                'phone_number' => $telephone['number']
            ],
            'business_address' => [
                'street' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->street)),
                'number' => $company->number ?? '',
                'district' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->neighborhood)),
                'city' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->city)),
                'state' => $company->state,
                'postal_code' => FoxUtils::onlyNumbers($company->zip_code),
                'suite' => empty($company->complement) ? '' : FoxUtils::removeAccents(
                    FoxUtils::removeSpecialChars($company->complement)
                ),
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
            'monthly_gross_income' => FoxUtils::onlyNumbers($company->monthly_gross_income),
            'federal_registration_status' => $company->present()->getFederalRegistrationStatus(),
            'founding_date' => $company->founding_date,
        ];
    }

    /**
     * @param Company $company
     * @return array
     */
    private function getPrepareDataComplementPjCompany(Company $company)
    {
//        $userInformation = $company->user->userInformation;

        return [
            'merchant_id' => $this->getMerchantId(),
            'subseller_id' => $company->subseller_getnet_id,
            'legal_document_number' => $company->company_document,
//            'legal_name' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
//            'date' => $company->founding_date,
//            'email' => $company->support_email,
            "working_hours" => [
                "start_day" => "mon",            // "mon" "tue" "wed" "thu" "fri" "sat" "sun"
                "end_day" => "mon",
                "start_time" => "08:00:00",      // "hh:mm:ss"
                "end_time" => "18:00:00"
            ],
            /*'addresses' => [
                'address_type' => 'business',
                'street' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->street)),
                'number' => $company->number ?? '',
                'district' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->neighborhood)),
                'city' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->city)),
                // @todo esta salvando a string inteira precisa ter somente codigo UF
                'state' => $company->state,
                'postal_code' => FoxUtils::onlyNumbers($company->zip_code),
                'suite' => empty($company->complement) ? '' : FoxUtils::removeAccents(
                    FoxUtils::removeSpecialChars($company->complement)
                ),
            ],*/
            /*'identification_document' => [
                'document_type' => 'nire',
                'document_number' => '',
                'document_issue_date' => '',
                'document_issuer' => '',
                'document_issuer_state' => ''
            ],*/
            /*'bank_accounts' => [
                'type_accounts' => 'unique',
                'unique_account' => [
                    'bank' => $company->bank,
                    'agency' => $company->agency . $company->agency_digit,
                    'account' => $company->account,
                    'account_type' => $company->present()->getAccountType(),
                    'account_digit' => ($company->account_digit == 'X' || $company->account_digit == 'x') ? 0 : $company->account_digit,
                ],
            ],*/
            'url_callback' => $this->urlCallback,
//            'payment_plan' => 3,
//            'marketplace_store' => "S",
//            'trade_name' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'state_fiscal_document_number' => $company->state_fiscal_document_number,
//            'federal_registration_status' => $company->present()->getFederalRegistrationStatus(),
            'federal_registration_status_date' => $company->federal_registration_status_date,
            'social_value' => $company->social_value,
//            'business_entity_type' => FoxUtils::onlyNumbers($company->business_entity_type),
            /*'economic_activity_classification_code' => FoxUtils::onlyNumbers(
                $company->economic_activity_classification_code
            ),*/
//            'monthly_gross_income' => FoxUtils::onlyNumbers($company->monthly_gross_income),
        ];
    }

    /**
     * @param Company $company
     * @return array
     * @throws PresenterException
     */
    private function getPrepareDataUpdatePjCompany(Company $company)
    {
        $user = $company->user;
        $telephone = FoxUtils::formatCellPhoneGetNet($user->cellphone);

        $country = 'BR';
        if ($company->country == 'usa') {
            $country = 'EUA';
        }

        return [
            'merchant_id' => $this->getMerchantId(),
            'subseller_id' => $company->subseller_getnet_id,
            'legal_document_number' => $company->company_document,
            'legal_name' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'trade_name' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'block_payments' => 'N',
            'block_transactions' => 'N',
            'business_entity_type' => FoxUtils::onlyNumbers($company->business_entity_type),
            'economic_activity_classification_code' => FoxUtils::onlyNumbers(
                $company->economic_activity_classification_code
            ),
            'state_fiscal_document_number' => $company->state_fiscal_document_number,
            'federal_registration_status' => $company->present()->getFederalRegistrationStatus(),
            'email' => $company->support_email,
            'business_address' => [
                'street' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->street)),
                'number' => $company->number ?? '',
                'district' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->neighborhood)),
                'city' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->city)),
                // @todo esta salvando STATE a string inteira precisa ter somente codigo UF
                'state' => $company->state,
                'postal_code' => FoxUtils::onlyNumbers($company->zip_code),
                'suite' => empty($company->complement) ? '' : FoxUtils::removeAccents(
                    FoxUtils::removeSpecialChars($company->complement)
                ),
                'country' => $country
            ],
            'phone' => [
                'area_code' => $telephone['dd'],
                'phone_number' => $telephone['number']
            ],
            'bank_accounts' => [
                'type_accounts' => 'unique',
                'unique_account' => [
                    'bank' => $company->bank,
                    'agency' => $company->agency . $company->agency_digit,
                    'account' => $company->account,
                    'account_type' => $company->account_type,
                    'account_digit' => $company->account_digit == 'X' || $company->account_digit == 'x' ? 0 : $company->account_digit,
                ],
            ],
            'liability_chargeback' => 'S',
            'marketplace_store' => 'S',
            'payment_plan' => 3,
        ];
    }

}

