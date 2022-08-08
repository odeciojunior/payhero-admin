<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\SaleGatewayRequest;

/**
 * Class GetnetService
 * @package App\Services
 */
class GetnetPaymentService
{
    const URL_HOMOLOG_API = "https://api-homologacao.getnet.com.br/";
    const URL_PRODUCTION_API = "https://api.getnet.com.br/";

    private $authorizationToken;
    private $gatewayId;
    private $urlApi;
    private $sellerId;
    private $accessToken;

    public const SUMMARY_TRANSACTION_TYPE_CREDITO_A_VISTA = 1;
    public const SUMMARY_TRANSACTION_TYPE_CREDITO_PARCELADO_LOJISTA = 2;
    public const SUMMARY_TRANSACTION_TYPE_CREDITO_PARCELADO_ADMINISTRADORA = 3;
    public const SUMMARY_TRANSACTION_TYPE_DEBITO = 4;
    public const SUMMARY_TRANSACTION_TYPE_CANCELAMENTO = 5;
    public const SUMMARY_TRANSACTION_TYPE_CHARGEBACK = 6;
    public const SUMMARY_TRANSACTION_TYPE_BOLETO = 7;

    public const TRANSACTION_STATUS_CODE_APROVADO = 0;
    public const TRANSACTION_STATUS_CODE_AGUARDANDO = 70;
    public const TRANSACTION_STATUS_CODE_PENDENTE = 77;
    public const TRANSACTION_STATUS_CODE_PENDENTE_PAGAMENTO = 78;
    public const TRANSACTION_STATUS_CODE_TIMEOUT = 83;
    public const TRANSACTION_STATUS_CODE_DESFEITA = 86;
    public const TRANSACTION_STATUS_CODE_INEXISTENTE = 90;
    public const TRANSACTION_STATUS_CODE_NEGADO_ADMINISTRADORA = 91;
    public const TRANSACTION_STATUS_CODE_ESTORNADA = 92;
    public const TRANSACTION_STATUS_CODE_REPETIDA = 93;
    public const TRANSACTION_STATUS_CODE_ESTORNADA_CONCILIACAO = 94;
    public const TRANSACTION_STATUS_CODE_CANCELADA_SEM_CONFIRMACAO = 98;
    public const TRANSACTION_STATUS_CODE_NEGADO_MGM = 99;

    public function __construct()
    {
        if (FoxUtils::isProduction()) {
            $gateway = Gateway::where("name", "getnet_production")->first();
            $this->urlApi = self::URL_PRODUCTION_API;
            $this->sellerId = env("GET_NET_SELLER_ID_PRODUCTION");
        } else {
            $gateway = Gateway::where("name", "getnet_sandbox")->first();
            $this->urlApi = self::URL_HOMOLOG_API;
            $this->sellerId = env("GET_NET_SELLER_ID_SANDBOX");
        }
        $configs = json_decode(FoxUtils::xorEncrypt($gateway->json_config, "decrypt"), true);

        $this->gatewayId = $gateway->id ?? null;
        $this->authorizationToken = base64_encode($configs["public_token"] . ":" . $configs["private_token"]);
        $this->setAccessToken();
    }

    public function getAuthorizationHeader(): array
    {
        return [
            "authorization: Bearer " . $this->accessToken,
            "Content-Type: application/json",
            "seller_id: " . $this->sellerId,
        ];
    }

    public function setAccessToken()
    {
        $headers = [
            "content-type: application/x-www-form-urlencoded",
            "authorization: Basic " . $this->authorizationToken,
        ];
        $data = "scope=oob&grant_type=client_credentials";
        $return = json_decode($this->sendCurl("auth/oauth/v2/token", "POST", $data, $headers));

        if (!empty($return->access_token)) {
            $this->accessToken = $return->access_token;
        } else {
            throw new Exception("Erro ao gerar token de acesso captura getnet");
        }
    }

    /**
     * @param $url
     * @param $method
     * @param null $data
     * @param null $headers
     * @return bool|string
     */
    private function sendCurl($url, $method, $data = null, $headers = null)
    {
        $curl = curl_init($this->urlApi . $url);
        $headers = is_null($headers) ? $this->getAuthorizationHeader() : $headers;
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        if (!is_null($data)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * @param $saleId
     * @param $paymentId
     * @param $amount
     */
    public function cancelPayment($saleId, $paymentId, $amount)
    {
        try {
            $data = array_filter([
                "payment_id" => $paymentId,
                "cancel_amount" => $amount,
            ]);

            $data = json_encode($data);

            $result = json_decode($this->sendCurl("v1/payments/cancel/request", "POST", $data));

            SaleGatewayRequest::create([
                "sale_id" => $saleId,
                "gateway_id" => $this->gatewayId,
                "send_data" => $data,
                "gateway_result" => json_encode($result),
                "gateway_exceptions" => null,
            ]);
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * @param $cancelCustomKey
     * @return mixed
     */
    public function getCancellationByCustomKey($cancelCustomKey)
    {
        return json_decode($this->sendCurl("v1/payments/cancel/request?cancel_custom_key=" . $cancelCustomKey, "GET"));
    }

    /**
     * @param $cancelRequestId
     * @return mixed
     */
    public function getCancellationByRequestId($cancelRequestId)
    {
        return json_decode($this->sendCurl("v1/payments/cancel/request/" . $cancelRequestId, "GET"));
    }

    public function getGatewayId()
    {
        return $this->gatewayId;
    }
}
