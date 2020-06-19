<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Modules\Core\Entities\GetnetBackofficeRequests;
use Modules\Core\Traits\GetNetFakeDataTrait;

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
        $url = self::URL_API . 'v1/mgm/pf/consult/paymentplans/' . $this->getMerchantId();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());

        $result     = curl_exec($curl);
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
        
    }

    public function updatePfCompany()
    {

    }

    public function getPfCompany()
    {

    }

    public function checkPfCompanyRegister()
    {

    }

}