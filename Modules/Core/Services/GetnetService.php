<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\GetnetBackofficeRequests;
use Modules\Core\Traits\GetNetFakeDataTrait;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Class GetnetService
 * @package Modules\Core\Services
 */
class GetnetService
{
    use GetNetFakeDataTrait;

    public const URL_API = 'https://api-homologacao.getnet.com.br/';

    public const URL_CALLBACK = 'https://app.cloudfox.net/postback/getnet';

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
     * @param string $cpf
     * Consulta complemento cadastral de um CPF
     */
    public function checkPfCompanyRegister(string $cpf)
    {
        $url = self::URL_API . 'v1/mgm/pf/callback/' . $this->getMerchantId() . '/' . $cpf;
        $data = $cpf;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->saveRequestsPjCompany($url, $result, $httpStatus, $data);
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
    }

    /**
     * @param Company $company
     * Cria pré-cadastro da loja PF
     */
    public function createPfCompany(Company $company)
    {
        $url = self::URL_API . 'v1/mgm/pf/create-presubseller';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->getPrepareDataCreatePfCompany($company)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
    }

    /**
     * @param Company $company
     * Complementar pré-cadastro da loja quando necessario
     */
    public function complementPfCompany(Company $company)
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
            http_build_query($this->getPrepareDataComplementePfCompany($company))
        );

        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
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

    /**
     * @param $company
     * @return array
     */
    private function getPrepareDataCreatePfCompany($company)
    {
        return [
            'merchant_id' => $this->getMerchantId(),
            'legal_document_number' => '',
            'legal_name' => '',
            'birth_date' => '',
            'mothers_name' => '',
            'occupation' => '',
            'monthly_gross_income' => '',
            'business_address' => [
                'mailing_address_equals' => '',
                'street' => '',
                'number' => '',
                'district' => '',
                'city' => '',
                'state' => '',
                'postal_code' => '',
                'suite' => '',
            ],
            "working_hours" => [
                [
                    "start_day" => "mon",            // "mon" "tue" "wed" "thu" "fri" "sat" "sun"
                    "end_day" => "mon",
                    "start_time" => "08:00:00",      // "hh:mm:ss"
                    "end_time" => "18:00:00"
                ],
            ],
            'cellphone' => [
                'area_code' => '',
                'phone_number' => ''
            ],
            'email' => '',
            'acquirer_merchant_category_code' => '2128',
            'bank_accounts' => [
                'type_accounts' => 'unique',
                'unique_account' => [
                    'bank' => '',
                    'agency' => '',
                    'account' => '',
                    'account_type' => '', // C conta corrente P conta poupança
                    'account_digit' => ''
                ],
            ],
            'url_callback' => self::URL_CALLBACK,
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
    private function getPrepareDataComplementePfCompany(Company $company)
    {
        return [
            'merchant_id' => $this->getMerchantId(),
            'subseller_id' => '',
            'legal_document_number' => '',
            'date' => '',
            'identification_document' => [
                'document_type' => '',
                'document_number' => '',
                'document_issuer_date' => '',
                'document_expiration_date' => '',
                'document_issuer' => '',
                'document_issuer_state' => '',
                'document_serial_number' => '',
            ],
            'url_callback' => self::URL_CALLBACK,
            'sex' => '',
            'marital_status' => '',
            'nationality' => '',
            'fathers_name' => '',
            'spouse_name' => '',
            'birth_place' => '',
            'birth_city' => '',
            'birth_state' => '',
            'birth_country' => '',
            'monthly_income' => '',
            'ppe_indication' => 'not_applied',
            'patrimony' => '',
        ];
    }


    /**
     * @param $cnpj
     */
    public function checkComplementPjCompanyRegister($cnpj)
    {
        $url = self::URL_API . 'v1/mgm/pj/consult/' . $this->getMerchantId() . '/' . $cnpj;
        $data = $cnpj;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->saveRequestsPjCompany($url, $result, $httpStatus, $data);
    }

    /**
     * @param $cnpj
     * Method GET
     * Consulta situação cadastral do CNPJ da loja
     */
    public function checkPjCompanyRegister($cnpj)
    {
        $url = self::URL_API . 'v1/mgm/pj/callback/' . $this->getMerchantId() . '/' . $cnpj;
        $data = $cnpj;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->saveRequestsPjCompany($url, $result, $httpStatus, $data);
    }

    /**
     * Method GET
     * Consulta planos de pagamentos connfigurados para loja PJ
     */
    public function checkAvailablePaymentPlansPj()
    {
        $url = self::URL_API . 'v1/mgm/pj/consult/paymentplans/' . $this->getMerchantId();
        $data = '';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $this->saveRequestsPjCompany($url, $result, $httpStatus, $data);
    }

    /**
     * @param Company $company
     * @throws PresenterException
     * Method POST
     * Cria pré-cadastro da loja
     */
    public function createPjCompany(Company $company)
    {
        $url = self::URL_API . 'v1/mgm/pj/create-presubseller';
        $data = $this->getPrepareDataCreatePjCompany($company);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $this->saveRequestsPjCompany($url, $result, $httpStatus, $data);
    }

    /**
     * @param Company $company
     * @throws PresenterException
     * Method PUT
     * Complementa pré-cadastro da loja se necessario
     */
    public function complementPjCompany(Company $company)
    {
        $url = self::URL_API . 'v1/mgm/pj/complement';
        $data = $this->getPrepareDataComplementPjCompany($company);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->saveRequestsPjCompany($url, $result, $httpStatus, $data);
    }

    /**
     * @param Company $company
     * @throws PresenterException
     * Method PUT
     * Atualiza cadastro da loja
     */
    public function updatePjCompany(Company $company)
    {
        $url = self::URL_API . 'v1/mgm/pj/update-subseller';
        $data = $this->getPrepareDataUpdatePjCompany($company);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_PUT, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->saveRequestsPjCompany($url, $result, $httpStatus, $data);
    }

    /**
     * @param $subsellerGetnetId
     * Method POST
     * Descredenciar Loja PJ
     */
    public function disqualifyPjCompany($subsellerGetnetId)
    {
        $url = self::URL_API . 'v1/mgm/pj/de-accredit/' . $this->getMerchantId() . '/' . $subsellerGetnetId;
        $data = $this->getAuthorizationHeader();
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $data);
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->saveRequestsPjCompany($url, $result, $httpStatus, $data);
    }

    /**
     * @param $url
     * @param $result
     * @param $httpStatus
     * @param $data
     */
    private function saveRequestsPjCompany($url, $result, $httpStatus, $data)
    {
        GetnetBackofficeRequests::create(
            [
                'sent_data' => json_encode(
                    [
                        'url' => $url,
                        'data' => $data
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
        $telephone = FoxUtils::formatCellPhoneGetNet($company->support_telephone);
        return [
            'merchant_id' => $this->getMerchantId(),
            'legal_document_number' => $company->company_document,
            'legal_name' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'trade_name' => FoxUtils::removeAccents(FoxUtils::removeSpecialChars($company->fantasy_name)),
            'state_fiscal_document_number' => $company->state_fiscal_document_number,
            'email' => $company->support_email,
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
                'country' => $company->country == 'brazil' ? 'BR' : 'BR'

            ],
            'bank_accounts' => [
                'type_accounts' => 'unique',
                'unique_account' => [
                    'bank' => $company->bank,
                    'agency' => $company->agency . $company->agency_digit,
                    'account' => $company->account,
                    'account_type' => $company->present()->getAccountType(),
                    'account_digit' => $company->account_digit == 'X' || $company->account_digit == 'x' ? 0 : $company->account_digit,
                ]
            ],
            'url_callback' => self::URL_CALLBACK,
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
     * @throws PresenterException
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
            'url_callback' => self::URL_CALLBACK,
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
                // @todo esta salvando a string inteira precisa ter somente dois caracteres
                'country' => $company->country
            ],
            'phone' => [
                'area_code' => substr($company->support_telephone, 0, 2),
                'phone_number' => $company->support_telephone
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

    // @todo
    // criar campos :
    //                  'liability_chargeback' => 'S',
    //                  'marketplace_store' => 'S',

}