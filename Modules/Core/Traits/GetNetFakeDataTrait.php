<?php

namespace Modules\Core\Traits;

trait GetNetFakeDataTrait
{
    public function getPfCompanyCreateTestData()
    {
        return [
            "merchant_id"=> $this->getMerchantId(),
            "legal_document_number"=> 84947611022,
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
        
    }

    public function getPfCompanyUpdateTestData()
    {

    }

}

