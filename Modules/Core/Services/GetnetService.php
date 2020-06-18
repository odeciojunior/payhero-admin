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

    public function getAuthorizationHeader()
    {
        return [
            'authorization: Bearer ' . $this->accessToken,
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
        $curl = curl_init(self::URL_API . 'v1/mgm/pj/consult/paymentplans/' . env('GET_NET_MERCHANT_ID'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl,CURLOPT_HEADER,true); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());

        $result     = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        dd($result, $httpStatus);
    }

    public function createPfCompany()
    {

    }
}