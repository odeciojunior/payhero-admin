<?php namespace Modules\Core\Services\Gateways;

use DB;
use Exception;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\FoxUtils;

class CheckoutGateway extends GatewayAbstract
{
    public $gateway_id;
    public $name_enum;
    public string $merchantId;
    
    public function __construct($gatewayId)
    {
        $this->name_enum = 'checkout';
        $this->loadEndpoints();
        
        $this->baseUrl = getenv('CHECKOUT_URL')."/api/";
        if (FoxUtils::isProduction()) {
           $this->baseUrl = "https://checkout.cloudfox.net/api/";
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

    public function getCurrentBalance($companyId=null){
        $options = new GatewayCurlOptions([
            'endpoint'=>'getCurrentBalance',  
            'variables'=>[$this->gatewayId,$companyId]            
        ]);
        return json_decode($this->requestHttp($options));
    }

    public function getAnticipationsAsaas($companyId){
        $options = new GatewayCurlOptions([
            'endpoint'=>'getAnticipationsAsaas',  
            'variables'=>[$companyId]            
        ]);
        return json_decode($this->requestHttp($options));
    }

    public function getAnticipationAsaas($companyId,$anticipationId){
        $options = new GatewayCurlOptions([
            'endpoint'=>'getAnticipationAsaas',  
            'variables'=>[$companyId,$anticipationId]            
        ]);
        return json_decode($this->requestHttp($options));
    }

    public function getTransfersAsaas($companyId){
        $options = new GatewayCurlOptions([
            'endpoint'=>'getTransfersAsaas',  
            'variables'=>[$companyId]            
        ]);
        return json_decode($this->requestHttp($options));
    }

    public function getTransferAsaas($companyId,$transferId){
        $options = new GatewayCurlOptions([
            'endpoint'=>'getTransferAsaas',  
            'variables'=>[$companyId,$transferId]            
        ]);
        return json_decode($this->requestHttp($options));
    }

    public function simulateWebhookTransfer($data){
        $this->baseUrl = getenv('CHECKOUT_URL')."/";
        $options = new GatewayCurlOptions([
            'endpoint'=>'simulateWebhookTransfer',  
            'data'=>$data
        ]);        
        $response = json_decode($this->requestHttp($options));
        $this->baseUrl = getenv('CHECKOUT_URL')."/api/";
        return $response;
    }

    public function transferSellerToSubSeller($companyId,int $amount){
        $options = new GatewayCurlOptions([
            'endpoint'=>'transferSellerToSubSeller',  
            'variables'=>[$companyId],
            'data' => ['amount'=>$amount]            
        ]);
        return json_decode($this->requestHttp($options));
    }

    public function transferSubSellerToSeller($companyId,int $amount,$transferId){
        $options = new GatewayCurlOptions([
            'endpoint'=>'transferSubSellerToSeller',  
            'variables'=>[$companyId],
            'data' => ['amount'=>$amount,'transfer_id'=>$transferId]            
        ]);
        return json_decode($this->requestHttp($options));   
    }

    public function setBaseUrl($newUrl){
        $this->baseUrl = $newUrl;
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
            "getAnticipationAsaas" => [
                "route" => "withdrawal/anticipation-asaas/:companyId/:anticipationId",
                "method" => "GET"
            ],
            "getAnticipationsAsaas" => [
                "route" => "withdrawal/anticipations-asaas/:companyId",
                "method" => "GET"
            ],
            "getTransfersAsaas" => [
                "route" => "withdrawal/transfers-asaas/:companyId",
                "method" => "GET"
            ],
            "getTransferAsaas" => [
                "route" => "withdrawal/transfer-asaas/:companyId/:transferId",
                "method" => "GET"
            ],
            "simulateWebhookTransfer" => [
                "route" => "postback/asaas",
                "method" => "POST"
            ],
            "transferSellerToSubSeller" => [
                "route" => "withdrawal/transfer-seller-to-subseller-asaas/:companyId",
                "method" => "POST"
            ],
            "transferSubSellerToSeller" => [
                "route" => "withdrawal/transfer-subseller-to-seller-asaas/:companyId",
                "method" => "POST"
            ],            
        ];
    }

    public function getUrl(){    
        return $this->baseUrl;
    }
}