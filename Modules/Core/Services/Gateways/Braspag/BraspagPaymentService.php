<?php

namespace Modules\Core\Services\Gateways\Braspag;

use Exception;
use GuzzleHttp\Client;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\BraspagService;
use Modules\Core\Services\FoxUtils;

class BraspagPaymentService extends BraspagService
{
    public $client;
    public $gateway;
    public $urlApi;
    public $urlApiAuth;
    public $cloudFoxClientId;
    public $cloudFoxClientSecret;
    public $accessToken;

    public function __construct()
    {
        if (FoxUtils::isProduction()) {
            $this->gateway    = Gateway::where("name", "braspag_production")->first();
            $this->urlApi     = 'https://api.cieloecommerce.cielo.com.br/';
            $this->urlApiAuth = 'https://auth.braspag.com.br/';
        } else {
            $this->gateway    = Gateway::where("name", "braspag_sandbox")->first();
            $this->urlApi     = 'https://apisandbox.cieloecommerce.cielo.com.br/';
            $this->urlApiAuth = 'https://authsandbox.braspag.com.br/';
        }

        if (empty($this->gateway)) {
            throw new Exception("Gateway braspag não encontrado no ambiente de " . env('APP_ENV'));
        }

        $configs = json_decode(FoxUtils::xorEncrypt($this->gateway->json_config, "decrypt"), true);

        $this->cloudFoxClientId     = $configs["public_token"];
        $this->cloudFoxClientSecret = $configs["private_token"];

        $this->client = new Client([]);

        $this->authenticated();
    }

    private function authenticated()
    {
        $url     = $this->urlApiAuth . 'oauth2/token';
        $options = [
            'headers'     => [
                'Authorization' => 'Basic ' . base64_encode($this->cloudFoxClientId . ':' . $this->cloudFoxClientSecret),
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ],
            'form_params' => ['grant_type' => 'client_credentials'],
        ];

        $response = $this->client->post($url, $options);
        $content  = $response->getBody()->getContents();
        $response = \GuzzleHttp\json_decode($content);

        $this->accessToken = $response->access_token;
    }

    public function getCompanyPaymentData(array $filters, $companyId)
    {
        $url = "schedule-api/events?initialForecastedDate={$filters['initial_date']}&finalForecastedDate={$filters['final_date']}&pageIndex={$filters['page_index']}&pageSize={$filters['page_size']}&eventStatus={$filters['event_status']}&merchantIds={$filters['merchant_id']}";

        return $this->sendCurl($url, 'GET', null, $companyId);
    }
}
