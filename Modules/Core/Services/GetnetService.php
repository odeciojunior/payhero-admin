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

    public function setAccessToken()
    {
        try {
            $headers = [
                'content-type: application/x-www-form-urlencoded',
                'authorization: Basic ' . $this->getAuthorizationToken(),
            ];

            $ch = curl_init(self::URL_API . 'credenciamento/auth/oauth/v2/token');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'scope=mgm&grant_type=client_credentials');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result     = curl_exec($ch);
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if($httpStatus == 200){
                $this->accessToken = json_decode($result)->access_token;
            }
            else{
                throw new Exception('Erro ao gerar token de acesso backoffice getnet');
            }

        } catch (Exception $ex) {
            report($ex);
        }

    }


}