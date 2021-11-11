<?php namespace Modules\Core\Services\Gateways;

use DB;
use Exception;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\FoxUtils;

class CheckoutGateway extends GatewayAbstract{

    public const URL_HOMOLOG = "\/api/";
    public const URL_PRODUCTION = 'https://checkout.cloudfox.net/api/';

    private $authorizationToken;
    private $accessToken;
    private $accessTokenMgm;
    public $gateway_id;
    public $name_enum;
    public string $merchantId;
    private string $sellerId;
    
    public function __construct($gatewayId)
    {
        $this->name_enum = 'checkout';
        $this->loadEndpoints();
        
        $this->baseUrl = getenv('CHECKOUT_URL')."/api/";
        if (FoxUtils::isProduction()) {
           $this->baseUrl = self::URL_PRODUCTION;
        } 
                
        $this->gatewayId = $gatewayId;
    }

    public function getDefaultHeader(){
        return [
            
            'Content-Type: application/json',
            'Accept: application/json',
            'Api-name:ADMIN',
            'Api-token:' . env('ADMIN_TOKEN')
        ];        
    }

    public function createAccount($data){
        $options = new GatewayCurlOptions([
            'endpoint'=>'createAccount',
            'variables'=>[$this->gatewayId],
            'data'=>$data
        ]);
        return json_decode($this->requestHttp($options));
    }

    public function registerWebhookTransferAsaas($companyId){
        $options = new GatewayCurlOptions([
            'endpoint'=>'registerWebhookTransferAsaas',            
            'data'=>['company_id'=>$companyId]
        ]);
        return json_decode($this->requestHttp($options));
    }

    public function getCurrentBalance($companyId){
        $options = new GatewayCurlOptions([
            'endpoint'=>'getCurrentBalance',  
            'variables'=>[$this->gatewayId,$companyId]            
        ]);
        return json_decode($this->requestHttp($options));
    }

    public function getCurlInfo(){
        return $this->curlInfo;
    }

    public function loadEndpoints(){
        $this->endpoints = [
            "registerWebhookTransferAsaas" => [
                "route" => "withdrawal/register-webhook-transfer-asaas",
                "method" => "POST"
            ],
            "createAccount" => [
                "route" => "withdrawal/create-account/:gatewayId",
                "method" => "POST"
            ],  
            "getCurrentBalance" => [
                "route" => "withdrawal/current-balance/:gatewayId/:companyId",
                "method" => "GET"
            ],                 
        ];
    }

    public function getUrl(){    
        return $this->baseUrl;
    }
}