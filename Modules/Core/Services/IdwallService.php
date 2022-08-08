<?php

namespace Modules\Core\Services;

class IdwallService
{
    private $accessToken;

    public function __construct()
    {
        $this->accessToken = getenv("IDWALL_TOKEN");
    }

    public function getGenerateProtocolByCNPJ($cnpj)
    {
        $url = "https://api-v2.idwall.co/relatorios";
        $data = [
            "matriz" => "CloudFox_cnpj",
            "parametros" => [
                "cnpj_numero" => $cnpj,
            ],
        ];

        return $this->sendCurl($url, "POST", $data);
    }

    public function getGenerateProtocolByCPF($cpf)
    {
        $url = "https://api-v2.idwall.co/relatorios";
        $data = [
            "matriz" => "CloudFox_gateway_cpf_manual",
            "parametros" => [
                "cpf_numero" => $cpf,
            ],
        ];

        return $this->sendCurl($url, "POST", $data);
    }

    public function getReportByProtocolNumber($protocolNumber)
    {
        $url = "https://api-v2.idwall.co/relatorios/" . $protocolNumber . "/dados";

        return $this->sendCurl($url, "GET");
    }

    private function sendCurl($url, $method, $data = null)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        if (!is_null($data)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: " . $this->accessToken,
            "Content-Type: application/json",
        ]);
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return $result;
    }
}
