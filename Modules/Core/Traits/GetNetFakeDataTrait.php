<?php

namespace Modules\Core\Traits;

trait GetNetFakeDataTrait
{
    public function getPfCompanyCreateTestData()
    {
        return [
            "merchant_id"=> $this->getMerchantId(),
            "legal_document_number"=> '37608099011',
            "legal_name"=> "fulano silva",
            "birth_date"=> "1990-06-18",
            "mothers_name"=> "beltrana silva",
            "occupation"=> "faz nada da vida",
            "monthly_gross_income"=> 2000.00,
            "business_address" => [
                "mailing_address_equals"=> "S",
                "street"=> "de baixo da ponte",
                "number"=> 100,
                "district"=> "centro",
                "city"=> "BAGE",
                "state"=> "RS",
                "postal_code"=> '96400600',
                "suite"=> "casa"
            ],
            "working_hours"=> [
                [
                    "start_day"=> "mon",            // "mon" "tue" "wed" "thu" "fri" "sat" "sun" 
                    "end_day"=> "mon",
                    "start_time"=> "08:00:00",      // "hh:mm:ss"
                    "end_time"=> "18:00:00"
                ]
            ],
            "phone"=> [
                "area_code"=> 51,
                "phone_number"=> 39999999
            ],
            "cellphone"=> [
                "area_code"=> 51,
                "phone_number"=> 999999999
            ],
            "email"=> "julio@cloudfox.net",
            "acquirer_merchant_category_code"=> "2128",  // VENDA DE TERCEIROS (MARKETPLACES)
            "bank_accounts"=> [
                "type_accounts"=> "unique",
                "unique_account"=> [
                    "bank"=> '001',
                    "agency"=> 150,
                    "account"=> 12345,
                    "account_type"=> "C", // C conta corrente P conta poupança
                    "account_digit"=> "2"
                ],
            ],
            "list_commissions"=> [
                [
                    "brand"=> "MASTERCARD",
                    "product"=> "CREDITO A VISTA",
                    "commission_percentage"=> 93.50,
                    "payment_plan"=> 3
                ]
            ],
            "url_callback"=> "https://app.cloudfox.net/postback/getnet",
            "accepted_contract"=> "S",
            "liability_chargeback"=> "S",
            "marketplace_store"=> "S",
            "payment_plan"=> 3
        ];
    }

    public function getPfCompanyComplementTestData()
    {
        
        return [
            "merchant_id"                           => "string",
            "subseller_id"                          => 0,
            "legal_document_number"                 => 0,
            "legal_name"                            => "string",
            "trade_name"                            => "string",
            "block_payments"                        => "S",
            "block_transactions"                    => "S",
            "business_entity_type"                  => 0,
            "economic_activity_classification_code" => 0,
            "state_fiscal_document_number"          => "string",
            "federal_registration_status"           => "active",
            "email"                                 => "string",
            "business_address"=> [
                "street"      => "string",
                "number"      => 0,
                "district"    => "string",
                "city"        => "string",
                "state"       => "string",
                "postal_code" => 0,
                "suite"       => "string",
                "country"     => "string"
            ],
            "phone"=> [            
                "area_code"    => 0,
                "phone_number" => 0
            ],
            "bank_accounts"=> [
                "type_accounts"  => "unique",
                "unique_account" => [
                    "bank"          => 0,
                    "agency"        => 0,
                    "account"       => 0,
                    "account_type"  => "C",
                    "account_digit" => "string"
                ],
                "custom_accounts"=> [
                    [
                        "brand"=> "MASTERCARD",
                        "bank"=> 0,
                        "agency"=> 0,
                        "account"=> 0,
                        "account_type"=> "string",
                        "account_digit"=> "string"
                    ]
                ]
            ],
            "list_commissions"=> [
                [
                    "brand"=> "MASTERCARD",
                    "product"=> "DEBITO A VISTA",
                    "commission_percentage"=> 0,
                    "payment_plan"=> 0
                ]
            ],
            "liability_chargeback"=> "S",
            "marketplace_store"=> "S",
            "payment_plan"=> 0,
            "legal_representative"=> [
                "name"=> "string",
                "birth_date"=> "2020-06-19T18=>08=>48Z",
                "cpf"=> 0
            ],
            "shareholders"=> [
                [
                    "fiscal_type"=> "natural_person",
                    "document"=> 0,
                    "participation"=> 0,
                    "name"=> "string",
                    "date"=> "2020-06-19T18=>08=>48Z",
                    "mother_name"=> "string"
                ]
            ]
        ];
    }

    public function getPfCompanyUpdateTestData()
    {

    }

}

