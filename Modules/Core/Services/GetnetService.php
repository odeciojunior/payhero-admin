<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class GetnetService
{
    const URL_API = 'https://api-homologacao.getnet.com.br/';

    private $accessToken;

    public function __construct()
    {
        $this->setAccessToken();
    }

    public function dumpAccessToken()
    {
        dd($this->accessToken);
    }

    public function getAuthorizationToken()
    {
        $clientId = getenv('GET_NET_CLIENT_ID');
        $clientSecret = getenv('GET_NET_CLIENT_SECRET');

        return base64_encode($clientId . ':' . $clientSecret);
    }

    public function getMerchantId()
    {
        return env('GET_NET_MERCHANT_ID');
    }

    public function getAuthorizationHeader()
    {
        return [
            'authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json',
        ];
    }

    public function setAccessToken()
    {
        $headers = [
            'content-type: application/x-www-form-urlencoded',
            'authorization: Basic ' . $this->getAuthorizationToken(),
        ];

        $curl = curl_init(self::URL_API . 'credenciamento/auth/oauth/v2/token');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'scope=mgm&grant_type=client_credentials');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $result     = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if($httpStatus == 200){
            $this->accessToken = json_decode($result)->access_token;
        }
        else{
            throw new Exception('Erro ao gerar token de acesso backoffice getnet');
        }
    }

    public function checkAvailablePaymentPlans()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::URL_API . 'v1/mgm/pj/consult/paymentplans/' . $this->getMerchantId());
        curl_setopt($curl, CURLOPT_ENCODING, '');
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());

        $result     = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        dd($result, $httpStatus);
    }

    public function createPfCompany()
    {
        $curl = curl_init(self::URL_API . 'v1/mgm/pf/create-presubseller');
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->getPfCompanyCreateTestData()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result     = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        dd($result, $httpStatus);
    }

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
                    "payment_plan"=> 2
                ]
            ],
            "url_callback"=> "string",
            "accepted_contract"=> "S",
            "liability_chargeback"=> "S",
            "marketplace_store"=> "S",
            "payment_plan"=> 2
        ];
    }

}