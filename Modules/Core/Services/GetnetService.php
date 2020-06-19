<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Modules\Core\Entities\GetnetBackofficeRequests;
use Modules\Core\Traits\GetNetFakeDataTrait;

/**
 * Class GetnetService
 * @package Modules\Core\Services
 */
class GetnetService
{
    use GetNetFakeDataTrait;

    const URL_API = 'https://api-homologacao.getnet.com.br/';

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
        $result     = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        GetnetBackofficeRequests::create([
            'sent_data' => json_encode([
                'url' => $url,
                'data' => $this->getPfCompanyCreateTestData()
            ]),
            'response' => json_encode([
                'result' => json_decode($result),
                'status' => $httpStatus
            ])
        ]);

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
        curl_setopt($curl, CURLOPT_POSTFIELDS,http_build_query($this->getPfCompanyComplementTestData($this->getMerchantId(), 12344123)));

        $result     = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        GetnetBackofficeRequests::create([
            'sent_data' => json_encode([
                'url' => $url,
                'data' => $this->getPfCompanyComplementTestData($this->getMerchantId(), 12344123)
            ]),
            'response' => json_encode([
                'result' => json_decode($result),
                'status' => $httpStatus
            ])
        ]);

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
        curl_setopt($curl, CURLOPT_POSTFIELDS,http_build_query($this->getPfCompanyUpdateTestData($this->getMerchantId(), 12344123)));

        $result     = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        GetnetBackofficeRequests::create([
            'sent_data' => json_encode([
                'url' => $url,
                'data' => $this->getPfCompanyUpdateTestData($this->getMerchantId(), 12344123)
            ]),
            'response' => json_encode([
                'result' => json_decode($result),
                'status' => $httpStatus
            ])
        ]);

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
     */
    public function createPjCompany()
    {
        $url = self::URL_API . 'v1/mgm/pj/create-presubseller';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->getPjCompanyCreateTestData()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $this->saveRequestsPjCompany($url, $result, $httpStatus);


        dd($result, $httpStatus);
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
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->getPfCompanyCreateTestData()));
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
     * Method POST
     * Descredenciar Loja PJ
     * @todo cnpj fixo
     */
    public function disqualifyPjCompany()
    {
        $url = self::URL_API . 'v1/mgm/pj/de-accredit/' . $this->getMerchantId() . '/700050664';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->getPjCompanyDesqualifyTestData()));
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


}