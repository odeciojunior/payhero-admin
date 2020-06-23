<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\GetnetBackofficeRequests;
use Modules\Core\Traits\GetNetFakeDataTrait;

/**
 * Class GetnetService
 * @package Modules\Core\Services
 */
class GetnetService
{
    use GetNetFakeDataTrait;

    public const URL_API = 'https://api-homologacao.getnet.com.br/';

    private $accessToken;

    public function __construct()
    {
        $this->setAccessToken();
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
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpStatus == 200) {
            $this->accessToken = json_decode($result)->access_token;
        } else {
            throw new Exception('Erro ao gerar token de acesso backoffice getnet');
        }
    }

    /**
     * Consulta planos de pagamentos configurados para a loja
     */
    public function checkAvailablePaymentPlans()
    {
        $url = self::URL_API . 'v1/mgm/pf/consult/paymentplans/' . $this->getMerchantId();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());

        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        dd($result);
    }

    public function createPfCompany()
    {
        $url = self::URL_API . 'v1/mgm/pf/create-presubseller';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->getPfCompanyCreateTestData()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        GetnetBackofficeRequests::create(
            [
                'sent_data' => json_encode(
                    [
                        'url' => $url,
                        'data' => $this->getPfCompanyCreateTestData()
                    ]
                ),
                'response' => json_encode(
                    [
                        'result' => json_decode($result),
                        'status' => $httpStatus
                    ]
                )
            ]
        );

        dd($result, $httpStatus);
    }

    public function complementPfCompany()
    {
        $url = self::URL_API . 'v1/mgm/pf/complement';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        // curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt(
            $curl,
            CURLOPT_POSTFIELDS,
            http_build_query($this->getPfCompanyComplementTestData($this->getMerchantId(), 12344123))
        );

        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        GetnetBackofficeRequests::create(
            [
                'sent_data' => json_encode(
                    [
                        'url' => $url,
                        'data' => $this->getPfCompanyComplementTestData($this->getMerchantId(), 12344123)
                    ]
                ),
                'response' => json_encode(
                    [
                        'result' => json_decode($result),
                        'status' => $httpStatus
                    ]
                )
            ]
        );

        dd($result, $httpStatus);
    }

    public function updatePfCompany()
    {
        $url = self::URL_API . 'v1/mgm/pf/update-subseller';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        // curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt(
            $curl,
            CURLOPT_POSTFIELDS,
            http_build_query($this->getPfCompanyUpdateTestData($this->getMerchantId(), 12344123))
        );

        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        GetnetBackofficeRequests::create(
            [
                'sent_data' => json_encode(
                    [
                        'url' => $url,
                        'data' => $this->getPfCompanyUpdateTestData($this->getMerchantId(), 12344123)
                    ]
                ),
                'response' => json_encode(
                    [
                        'result' => json_decode($result),
                        'status' => $httpStatus
                    ]
                )
            ]
        );

        dd($result, $httpStatus);
    }

    public function getPfCompany()
    {
    }

    public function checkPfCompanyRegister()
    {
    }

    /**
     * Method GET
     * Consulta situação cadastral do CNPJ da loja
     * @todo CNPJ fixo por enquanto
     */
    public function checkPjCompanyRegister()
    {
        $url = self::URL_API . 'v1/mgm/pj/callback/' . $this->getMerchantId() . '/' . 28337339000105;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->saveRequestsPjCompany($url, $result, $httpStatus);
    }

    /**
     * Method GET
     * Consulta planos de pagamentos connfigurados para loja PJ
     */
    public function checkAvailablePaymentPlansPj()
    {
        $url = self::URL_API . 'v1/mgm/pj/consult/paymentplans/' . $this->getMerchantId();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $this->saveRequestsPjCompany($url, $result, $httpStatus);
    }

    /**
     * Method POST
     * Cria pré-cadastro da loja
     * @param Company $company
     */
    public function createPjCompany(Company $company)
    {
        $url = self::URL_API . 'v1/mgm/pj/create-presubseller';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->getPrepareDataCreatePjCompany($company)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $this->saveRequestsPjCompany($url, $result, $httpStatus);
    }

    /**
     * Method PUT
     * Complementa pré-cadastro da loja se necessario
     */
    public function complementPjCompany()
    {
        $url = self::URL_API . 'v1/mgm/pj/complement';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_PUT, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->getPjCompanyUpdateTestData()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->saveRequestsPjCompany($url, $result, $httpStatus);
    }

    /**
     * Method PUT
     * Atualiza cadastro da loja
     */
    public function updatePjCompany()
    {
        $url = self::URL_API . 'v1/mgm/pj/update-subseller';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_PUT, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->getPfCompanyCreateTestData()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->saveRequestsPjCompany($url, $result, $httpStatus);
    }

    /**
     * @param Company $company
     * Method POST
     * Descredenciar Loja PJ
     */
    public function disqualifyPjCompany(Company $company)
    {
        $url = self::URL_API . 'v1/mgm/pj/de-accredit/' . $this->getMerchantId() . '/' . $company->subseller_getnet_id;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->saveRequestsPjCompany($url, $result, $httpStatus);
    }


    public function saveRequestsPjCompany($url, $result, $httpStatus)
    {
        GetnetBackofficeRequests::create(
            [
                'sent_data' => json_encode(
                    [
                        'url' => $url,
                        'data' => $this->getPjCompanyCreateTestData()
                    ]
                ),
                'response' => json_encode(
                    [
                        'result' => $result,
                        'status' => $httpStatus
                    ]
                )
            ]

        );
    }

    /**
     * @param Company $company
     * @return array
     * @throws PresenterException
     */
    private function getPrepareDataCreatePjCompany(Company $company)
    {
        return [
            'merchant_id' => $this->getMerchantId(),
            'legal_document_number' => $company->company_document,
            'legal_name' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'trade_name' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'state_fiscal_document_number' => empty($company->state_fiscal_document_number) ? 'ISENTO' : $company->state_fiscal_document_number,
            'email' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->support_email)),
            'cellphone' => [
                'area_code' => substr($company->support_telephone, 0, 2),
                'phone_number' => $company->support_telephone
            ],
            'business_address' => [
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
                // @todo esta salvando a string inteira precisa ter somente dois caracteres
                'country' => $company->country

            ],
            'bank_accounts' => [
                'type_accounts' => 'unique',
                'unique_accounts' => [
                    'bank' => $company->bank,
                    'agency' => $company->agency . $company->agency_digit,
                    'account' => $company->account,
                    'account_type' => $company->account_type,
                    'account_digit' => $company->account_digit == 'X' || $company->account_digit == 'x' ? 0 : $company->account_digit,
                ]
            ],
            'url_callback' => "https://app.cloudfox.net/postback/getnet",
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

    private function getComplementPjCompany(Company $company)
    {
        $userInformation = $company->user->userInformation;
        return [
            'merchant_id' => $this->getMerchantId(),
            'legal_document_number' => $company->company_document,
            'legal_name' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'date' => $company->founding_date,
            'email' => $company->support_email,
            "working_hours" => [
                [
                    "start_day" => "mon",            // "mon" "tue" "wed" "thu" "fri" "sat" "sun"
                    "end_day" => "mon",
                    "start_time" => "08:00:00",      // "hh:mm:ss"
                    "end_time" => "18:00:00"
                ],
            ],
            'phones' => [
                'area_code' => substr($company->support_telephone, 0, 2),
                'phone_number' => $company->support_telephone
            ],
            'addresses' => [
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
            ],
            'identification_document' => [
                'document_type' => 'nire',
                'document_number' => FoxUtils::onlyNumbers($userInformation->document_number),
                'document_issue_date' => $userInformation->document_issue_date,
                'document_issuer' => $userInformation->document_issuer,
                'document_issuer_state' => $userInformation->document_issuer_state
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
            'url_callback' => "https://app.cloudfox.net/postback/getnet",
            'payment_plan' => 3,
            'marketplace_store' => "S",
            'trade_name' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'state_fiscal_document_number' => $company->state_fiscal_document_number,
            'federal_registration_status' => $company->present()->getFederalRegistrationStatus(),
            'federal_registration_status_date' => $company->federal_registration_status_date,
            'social_value' => $company->social_value,
            'business_entity_type' => FoxUtils::onlyNumbers($company->business_entity_type),
            'economic_activity_classification_code' => FoxUtils::onlyNumbers(
                $company->economic_activity_classification_code
            ),
            'monthly_gross_income' => FoxUtils::onlyNumbers($company->monthly_gross_income),
        ];
    }


}