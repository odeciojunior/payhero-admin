<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\GetnetBackofficeRequests;

/**
 * Class GetnetService
 * @package Modules\Core\Services
 */
class GetnetBaseService
{
    public $accessToken;

    public function __construct()
    {
    }

    public function getUrlApi()
    {
        if (FoxUtils::isProduction()) {
            return "https://api-backoffice.getnet.com.br/";
        }
        return "https://api-homologacao.getnet.com.br/";
    }

    /**
     * @param $url
     * @param $postFields
     * @throws Exception
     */
    public function setAccessToken($url, $postFields)
    {
        $headers = [
            "content-type: application/x-www-form-urlencoded",
            "authorization: Basic " . $this->authorizationToken,
        ];

        $curl = curl_init($this->getUrlApi() . $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpStatus == 200) {
            $this->accessToken = json_decode($result)->access_token;
        } else {
            throw new Exception("Erro ao gerar token de acesso captura getnet");
        }
    }

    /**
     * @param $url
     * @param $method
     * @param null $data
     * @param null $companyId
     * @param bool $saveLog
     * @return bool|string
     */
    public function sendCurl($url, $method, $data = null, $companyId = null, $saveLog = true)
    {
        $curl = curl_init($this->getUrlApi() . $url);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        if (!is_null($data)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($saveLog) {
            $this->saveRequests($url, $result, $httpStatus, $data, $companyId);
        }
        return $result;
    }

    /**
     * @param $url
     * @param $result
     * @param $httpStatus
     * @param $data
     * @param $companyId
     */
    public function saveRequests($url, $result, $httpStatus, $data, $companyId)
    {
        GetnetBackofficeRequests::create([
            "company_id" => $companyId,
            "sent_data" => json_encode([
                "url" => $url,
                "data" => $data,
            ]),
            "response" => json_encode([
                "result" => $result,
                "status" => $httpStatus,
            ]),
        ]);
    }
}
