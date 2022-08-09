<?php

namespace Modules\Core\Traits;

use Carbon\Carbon;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\FoxUtils;

trait GetnetPrepareCompanyData
{
    private string $urlCallback = "https://sirius.cloudfox.net/postback/getnet";

    public function getPrepareDataCreatePfCompany(Company $company)
    {
        $user = $company->user;

        $telephone = FoxUtils::formatCellPhoneGetNet($user->cellphone);

        $motherName = $user->mother_name;

        if (
            empty($motherName) &&
            !FoxUtils::isEmpty($user->id_wall_result) &&
            !empty(json_decode($user->id_wall_result, true)["result"]["pessoas_relacionadas"])
        ) {
            $idwall = json_decode($user->id_wall_result, true);
            $motherName = current(
                array_map(function ($item) use ($user) {
                    if (isset($item["tipo"]) && $item["tipo"] == "MAE") {
                        return $item["nome"];
                    }
                    return $user->mother_name;
                }, $idwall["result"]["pessoas_relacionadas"])
            );
        }

        $returnData = [
            "merchant_id" => $this->getMerchantId(),
            "legal_document_number" => FoxUtils::onlyNumbers($company->document),
            "legal_name" => FoxUtils::removeSpecialChars(FoxUtils::removeAccents($user->name)),
            "birth_date" => $user->date_birth,
            "mothers_name" => FoxUtils::removeSpecialChars(FoxUtils::removeAccents($motherName)),
            "occupation" => "vendedor",
            "business_address" => [
                "mailing_address_equals" => "S",
                "street" => FoxUtils::removeSpecialChars(FoxUtils::removeAccents($user->street)),
                "district" => FoxUtils::removeSpecialChars(FoxUtils::removeAccents($user->neighborhood)),
                "city" => FoxUtils::removeSpecialChars(FoxUtils::removeAccents($user->city)),
                "state" => FoxUtils::getFormatState($user->state),
                "number" => FoxUtils::onlyNumbers($user->number) ?? 0,
                "postal_code" => FoxUtils::onlyNumbers($user->zip_code),
            ],
            "working_hours" => [
                "start_day" => "mon", // "mon" "tue" "wed" "thu" "fri" "sat" "sun"
                "end_day" => "fri",
                "start_time" => "08:00:00", // "hh:mm:ss"
                "end_time" => "18:00:00",
            ],
            "cellphone" => [
                "area_code" => $telephone["dd"],
                "phone_number" => $telephone["number"],
            ],
            "email" => $user->email,
            "acquirer_merchant_category_code" => "2119",
            "list_commissions" => $this->getListCommissions($company),
            "url_callback" => $this->urlCallback,
            "accepted_contract" => "S",
            "liability_chargeback" => "S",
            "marketplace_store" => "S",
            "payment_plan" => 3,
        ];

        $bankAccount = $company->getBankAccountTED();
        if (!empty($bankAccount)) {
            $returnData["bank_accounts"] = [
                "type_accounts" => "unique",
                "unique_account" => [
                    "bank" => $bankAccount->bank,
                    "agency" => $bankAccount->agency,
                    "agency_digit" => $bankAccount->agency_digit,
                    "account" => $bankAccount->account,
                    "account_type" => "C", // Conta Corrente
                    "account_digit" => $bankAccount->account_digit,
                ],
            ];
        } else {
            $returnData["bank_accounts"] = [
                "type_accounts" => "unique",
                "unique_account" => [
                    "bank" => "001",
                    "agency" => "1111",
                    "agency_digit" => "",
                    "account" => ($company->company_type == 1 ? "1" : "3") . "11111111",
                    "account_type" => "C", // Conta Corrente
                    "account_digit" => "1",
                ],
            ];
        }

        return $returnData;
    }

    public function getPrepareDataComplementPfCompany(Company $company)
    {
        return [];
    }

    public function getPrepareDataUpdatePfCompany(Company $company)
    {
        return [
            "merchant_id" => $this->getMerchantId(),
            "subseller_id" => FoxUtils::isProduction()
                ? $company->getGatewaySubsellerId(Gateway::GETNET_PRODUCTION_ID)
                : $company->getGatewaySubsellerId(Gateway::GETNET_SANDBOX_ID),
            "legal_document_number" => FoxUtils::onlyNumbers($company->document),
        ];
    }

    private function getDataToCreatePjCompany(Company $company)
    {
        $user = $company->user;
        $telephone = FoxUtils::formatCellPhoneGetNet($user->cellphone);

        if (!FoxUtils::isEmpty($company->id_wall_result)) {
            $idWall = json_decode($company->id_wall_result, true)["result"];

            $founding_date = Carbon::createFromFormat("d/m/Y", $idWall["cnpj"]["data_abertura"])->format("Y-d-m");
            $economic_activity_classification_code = FoxUtils::onlyNumbers($idWall["cnpj"]["atividade_principal"]);
            $legal_name = FoxUtils::removeAccents($idWall["cnpj"]["nome_empresarial"]);
            $trade_name = FoxUtils::removeAccents($idWall["cnpj"]["nome_fantasia"]);
        }

        $returnData = [
            "merchant_id" => $this->getMerchantId(),
            "legal_document_number" => FoxUtils::onlyNumbers($company->document),
            "legal_name" =>
                $legal_name ?? FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            "trade_name" =>
                $trade_name ?? FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            "state_fiscal_document_number" => "ISENTO",
            "email" => $user->email,
            "cellphone" => [
                "area_code" => $telephone["dd"],
                "phone_number" => $telephone["number"],
            ],
            "business_address" => [
                "street" => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->street)),
                "number" => $company->number == null ? "" : $company->number,
                "district" => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->neighborhood)),
                "city" => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->city)),
                "state" => FoxUtils::getFormatState($company->state),
                "postal_code" => FoxUtils::onlyNumbers($company->zip_code),
                "country" => "BR",
            ],
            "url_callback" => $this->urlCallback,
            "accepted_contract" => "S",
            "liability_chargeback" => "S",
            "marketplace_store" => "S",
            "payment_plan" => 3,
            "economic_activity_classification_code" => $economic_activity_classification_code ?? 0,
            "federal_registration_status" => "active",
            "founding_date" => $founding_date ?? null,
        ];

        $bankAccount = $company->getBankAccountTED();
        if (!empty($bankAccount)) {
            $returnData["bank_accounts"] = [
                "type_accounts" => "unique",
                "unique_account" => [
                    "bank" => $bankAccount->bank,
                    "agency" => $bankAccount->agency,
                    "agency_digit" => $bankAccount->agency_digit,
                    "account" => $bankAccount->account,
                    "account_type" => "C", // Conta Corrente
                    "account_digit" =>
                        $bankAccount->account_digit == "X" || $bankAccount->account_digit == "x"
                            ? 0
                            : $bankAccount->account_digit,
                ],
            ];
        } else {
            $returnData["bank_accounts"] = [
                "type_accounts" => "unique",
                "unique_account" => [
                    "bank" => "001",
                    "agency" => "1111",
                    "agency_digit" => "",
                    "account" => ($company->company_type == 1 ? "1" : "3") . "11111111",
                    "account_type" => "C", // Conta Corrente
                    "account_digit" => "1",
                ],
            ];
        }

        return $returnData;
    }

    private function getPrepareDataComplementPjCompany(Company $company)
    {
        return [
            "merchant_id" => $this->getMerchantId(),
            "subseller_id" => $company->getGatewaySubsellerId(Gateway::GETNET_PRODUCTION_ID),
            "legal_document_number" => $company->document,
            "working_hours" => [
                "start_day" => "mon",
                "end_day" => "mon",
                "start_time" => "08:00:00",
                "end_time" => "18:00:00",
            ],
            "identification_document" => [
                "document_type" => "nire",
                "document_number" => "",
                "document_issue_date" => "", //$company->document_issue_date,
                "document_issuer" => "", //$company->document_issuer,
                "document_issuer_state" => "", //$company->document_issuer_state
            ],
            "federal_registration_status_date" => null,
            "social_value" => null,
        ];
    }

    private function getPrepareDataUpdatePjCompany(Company $company)
    {
        return [
            "merchant_id" => $this->getMerchantId(),
            "subseller_id" => FoxUtils::isProduction()
                ? $company->getGatewaySubsellerId(Gateway::GETNET_PRODUCTION_ID)
                : $company->getGatewaySubsellerId(Gateway::GETNET_SANDBOX_ID),
            "legal_document_number" => $company->document,
        ];
    }

    public function getListCommissions($gatewayData)
    {
        $listCommissions = [];

        $brands = ["MASTERCARD", "MAESTRO", "VISA", "VISA ELECTRON", "AMEX", "ELO CRÉDITO", "ELO DÉBITO", "HIPERCARD"];

        $products = [
            "DEBITO A VISTA",
            "CREDITO A VISTA",
            "PARCELADO LOJISTA 3X",
            "PARCELADO LOJISTA 6X",
            "PARCELADO LOJISTA 9X",
            "PARCELADO LOJISTA 12X",
            "PARCELADO EMISSOR",
            "BOLETO",
        ];

        foreach ($brands as $brand) {
            foreach ($products as $product) {
                if (!in_array([$product, $brand], $listCommissions)) {
                    if (in_array($brand, $brands) && ($product == "DEBITO A VISTA" || $product == "BOLETO")) {
                        continue;
                    }

                    if (
                        in_array($brand, ["MAESTRO", "VISA ELECTRON", "ELO DÉBITO"]) &&
                        in_array($product, [
                            "CREDITO A VISTA",
                            "PARCELADO LOJISTA 3X",
                            "PARCELADO LOJISTA 6X",
                            "PARCELADO LOJISTA 9X",
                            "PARCELADO LOJISTA 12X",
                            "PARCELADO EMISSOR",
                        ])
                    ) {
                        continue;
                    }

                    $listCommissions[] = [
                        "brand" => $brand,
                        "product" => $product,
                        "commission_percentage" => 100 - $gatewayData["gateway_tax"],
                        "payment_plan" => $this->getPlans($gatewayData["gateway_release_money_days"]),
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
            return ["payment_plan" => $paymentPlanId];
        }

        return null;
    }

    private function getPlans($releaseMoneyDays)
    {
        $plans = [
            "plans" => [
                "days" => [
                    2 => 3,
                    15 => 8,
                    30 => 5,
                ],
            ],
        ];

        return $plans["plans"]["days"][$releaseMoneyDays];
    }
}
