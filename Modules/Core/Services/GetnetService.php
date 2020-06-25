<?php

namespace Modules\Core\Services;

use Exception;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\GetnetBackofficeRequests;
use Modules\Core\Traits\GetNetPrepareDataTrait;

/**
 * Class GetnetService
 * @package Modules\Core\Services
 */
class GetnetService
{
    use GetNetPrepareDataTrait;

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
     * @param string $cpf
     * Consulta complemento cadastral de um CPF
     */
    public function checkPfCompanyRegister(string $cpf)
    {
        $url = self::URL_API . 'v1/mgm/pf/callback/' . $this->getMerchantId() . '/' . $cpf;
        $data = $cpf;
        $curl = curl_init();

        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => $this->getAuthorizationHeader()
            ]
        );

        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->saveRequests($url, $result, $httpStatus, $data);
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
     * @throws PresenterException
     * Cria pré-cadastro da loja PF
     */
    public function createPfCompany(Company $company)
    {
        $url = self::URL_API . 'v1/mgm/pf/create-presubseller';
        $data = $this->getPrepareDataCreatePfCompany($company);

        $curl = curl_init($url);


        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());

        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $this->saveRequests($url, $result, $httpStatus, $data);

        if ($httpStatus == 200) {
            return [
                'message' => 'Success',
                'data' => json_decode($result)
            ];
        } else {
            return [
                'message' => 'Error',
                'data' => json_decode($result)
            ];
        }
    }

    /**
     * @param Company $company
     * Complementar pré-cadastro da loja quando necessario
     */
    public function complementPfCompany(Company $company)
    {
        $url = self::URL_API . 'v1/mgm/pf/complement';
        $data = $this->getPrepareDataComplementPfCompany($company);
        $curl = curl_init();

        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_POST => true,
                CURLOPT_ENCODING => '',
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_HTTPHEADER => $this->getAuthorizationHeader(),
                CURLOPT_POSTFIELDS => json_encode($data)
            ]
        );


        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $this->saveRequests($url, $result, $httpStatus, $data);
    }

    public function updatePfCompany(Company $company)
    {
        $url = self::URL_API . 'v1/mgm/pf/update-subseller';
        $data = $this->getPrepareDataUpdatePfCompany($company);

        $curl = curl_init();
        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_POST => true,
                CURLOPT_ENCODING => '',
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_HTTPHEADER => $this->getAuthorizationHeader(),
                CURLOPT_POSTFIELDS => json_encode($data)
            ]
        );

        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->saveRequests($url, $result, $httpStatus, $data);
    }

    /**
     * @param $cnpj
     */
    public function checkComplementPjCompanyRegister($cnpj)
    {
        $url = self::URL_API . 'v1/mgm/pj/consult/' . $this->getMerchantId() . '/' . $cnpj;
        $data = $cnpj;
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => $this->getAuthorizationHeader()
            ]
        );
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->saveRequests($url, $result, $httpStatus, $data);
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
        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => $this->getAuthorizationHeader()
            ]
        );
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->saveRequests($url, $result, $httpStatus, $data);
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

        $this->saveRequests($url, $result, $httpStatus, $data);
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
        $this->saveRequests($url, $result, $httpStatus, $data);


        if ($httpStatus == 200) {
            return [
                'message' => 'success',
                'data' => json_decode($result)
            ];
        } else {
            return [
                'message' => 'error',
                'data' => json_decode($result)
            ];
        }
    }

    /**
     * @param Company $company
     * Method PUT
     * Complementa pré-cadastro da loja se necessario
     */
    public function complementPjCompany(Company $company)
    {
        $url = self::URL_API . 'v1/mgm/pj/complement';
        $data = $this->getPrepareDataComplementPjCompany($company);
        $curl = curl_init();

        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_POST => true,
                CURLOPT_ENCODING => '',
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_HTTPHEADER => $this->getAuthorizationHeader(),
                CURLOPT_POSTFIELDS => json_encode($data)
            ]
        );

        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->saveRequests($url, $result, $httpStatus, $data);
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

        $curl = curl_init();
        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_POST => true,
                CURLOPT_ENCODING => '',
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_HTTPHEADER => $this->getAuthorizationHeader(),
                CURLOPT_POSTFIELDS => json_encode($data)
            ]
        );

        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->saveRequests($url, $result, $httpStatus, $data);
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
        $this->saveRequests($url, $result, $httpStatus, $data);
    }

    /**
     * @param $url
     * @param $result
     * @param $httpStatus
     * @param $data
     */
    private function saveRequests($url, $result, $httpStatus, $data)
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



    // @todo
    // criar campos :
    //                  'liability_chargeback' => 'S',
    //                  'marketplace_store' => 'S',

}