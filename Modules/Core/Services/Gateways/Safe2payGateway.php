<?php namespace Modules\Core\Services\Gateways;


use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\FoxUtils;

class Safe2payGateway extends GatewayAbstract
{
    public const PAYMENT_STATUS_CONFIRMED = 'CONFIRMED';

    public $apiKey;
    public $name_enum;

    public function __construct()
    {
        $this->name_enum = 'safe2pay';
        $this->loadEndpoints();

        $this->baseUrl = "https://payment.safe2pay.com.br/";            
        $this->baseUrlApi = "https://api.safe2pay.com.br/";            

        if(foxutils()->isProduction()){
            $gateway = DB::table('gateways')->select('id','json_config')->where("id", Gateway::SAFE2PAY_PRODUCTION_ID)->first();
        }else{
            $gateway = DB::table('gateways')->select('id','json_config')->where("id", Gateway::SAFE2PAY_SANDBOX_ID)->first();                  
        }

        $configs = json_decode(FoxUtils::xorEncrypt($gateway->json_config, "decrypt"), true);

        $this->apiKey = $configs['token'];
        
        $this->gatewayId = $gateway->id;
    }

    public function setApiKey($apiKey = null)
    {
        $this->apiKey = $apiKey;
    }

    public function getDefaultHeader(){
        return [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-API-KEY: ' . $this->apiKey,
        ];
    }

    public function getTransaction($transactionId){
        $options = new GatewayCurlOptions([
            'baseUrl'=>$this->baseUrlApi,
            'endpoint' => 'getTransaction',
            'queryString' => ['Id'=>$transactionId]
        ]);

        return json_decode($this->requestHttp($options)); 
    }
 
    public function resendWebhook($idTransaction){  
        $headers = $this->getDefaultHeader();
        $headers[] = 'Content-Length: 0';
        $options = new GatewayCurlOptions([ 
            'baseUrl'=>$this->baseUrlApi,           
            'endpoint' => 'resendWebhook',
            'queryString'=>['idTransaction'=>$idTransaction],
            'headers'=>$headers
        ]);

        return json_decode($this->requestHttp($options));    
    }

    public function listTransactions($queryString){
        $options = new GatewayCurlOptions([     
            'baseUrl'=>$this->baseUrlApi,       
            'endpoint' => 'listTransactions',
            'queryString'=>$queryString
        ]);

        return json_decode($this->requestHttp($options));
    }

    public function listChargebacks($queryString){
        $options = new GatewayCurlOptions([     
            'baseUrl'=>$this->baseUrlApi,       
            'endpoint' => 'listChargebacks',
            'queryString'=>$queryString
        ]);

        return json_decode($this->requestHttp($options));
    }

    public function loadEndpoints(){
        $this->endpoints = [
            "getTransaction" => [
                "route" => "v2/transaction/Get",
                "method" => "GET"
            ],            
            "resendWebhook" => [
                "route" => "v2/Transaction/ResubmitCallback",
                "method" => "POST"
            ],
            "listTransactions" => [
                "route" => "v2/Transaction/List",
                "method" => "GET"
            ],  
            "listChargebacks"=> [
                "route" => "v2/Chargeback/List",
                "method" => "GET"
            ], 
        ];
    }

       
}