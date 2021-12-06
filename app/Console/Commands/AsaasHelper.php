<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\Gateways\CheckoutGateway;
use Symfony\Component\VarDumper\VarDumper;

class AsaasHelper extends Command
{
    protected $signature = 'asaas-helper';

    protected $description = 'Consultas na api do Asaas pelo checkout';

    private $api= null;

    public function __construct()
    {             
        parent::__construct();    
        $this->api = new CheckoutGateway(FoxUtils::isProduction()? Gateway::ASAAS_PRODUCTION_ID:Gateway::ASAAS_SANDBOX_ID);        
    }

    public function handle()
    {
        $this->listOptions();

        while(true){
            $option = $this->ask("Digite uma opção");
            $this->menu($option);
        }        
    }

    public function listOptions(){
        
        if(FoxUtils::isProduction()){
            $this->question('===== ASAAS (Production) =====');
        }else{
            $this->error('===== ASAAS (Sandbox) =====');
        }
        $this->comment("[1] Listar opções");        
        $this->comment("[2] Company Transfers ");
        $this->comment("[3] Company Transfer");
        $this->comment("[4] Company Balance");
        $this->comment("[5] Cloudfox Balance");
        $this->comment("[6] Company Anticipation");
        $this->comment("[7] Company Anticipations");
        $this->comment("[8] New Customer Loan");
        $this->comment("[0] Sair");
        $this->comment("===========================");
        $this->comment("[2964,3442] - João, Dani");
        $this->comment("===========================");
    }

    public function menu($option){
        
        switch($option){
            case 1:
                $this->listOptions();
            break;
            case 2:
                $this->getTransfers();
            break;
            case 3:
                $this->getTransfer();
            break;
            case 4:
                $this->getCompanyBalance();
            break;
            case 5:
                $this->getCloudfoxBalance();
            break;
            case 6:
                $this->getCompanyAntipation();
            break;
            case 7:
                $this->getCompanyAntipations();
            break;
            case 8:
                $this->clientLoan();
            break;           
            case 0:  
                $this->info('Bye!');              
                exit;
            break;
            default:
                $this->comment('Opção inválida');
            break;
        }
    }

    public function getTransfers()
    {
        $companyId = $this->anticipate('Informe CompanyId', ['2964', '3442']);  
        VarDumper::dump($this->api->getTransfersAsaas($companyId)??[]);
    }

    public function getTransfer()
    {
        list($companyId,$transferId) = explode(',',$this->ask('Informe CompanyId,transferId'));
        
        VarDumper::dump($this->api->getTransferAsaas($companyId,$transferId)??[]);
    }

    public function getCompanyBalance(){
        $companyId  = $this->anticipate('Informe CompanyId', ['2964', '3442']);
        VarDumper::dump($this->api->getCurrentBalance($companyId)??[]);
    }

    public function getCloudfoxBalance(){
        VarDumper::dump($this->api->getCurrentBalance()??[]);
    }

    public function getCompanyAntipation(){
        list($companyId,$anticipationId) = explode(',',$this->ask('Informe CompanyId,anticipationId'));
        
        VarDumper::dump($this->api->getAnticipationAsaas($companyId,$anticipationId)??[]);
    }

    public function getCompanyAntipations(){
        $companyId = $this->anticipate('Informe CompanyId', ['2964', '3442']);
        
        VarDumper::dump($this->api->getAnticipationsAsaas($companyId)??[]);
    }

    public function simulateWebhookTransfer(){
        $data = [            
            "event"=>"TRANSFER_PENDING",
            "transfer"=>[
                "object"=>"transfer",
                "id"=>"777eb7c8-b1a2-4356-8fd8-a1b0644b5282",
                "dateCreated"=>"2019-05-02",
                "status"=>"PENDING",
                "effectiveDate"=>null,
                "type"=>"BANK_ACCOUNT",
                "value"=>1000,
                "netValue"=>1000,
                "transferFee"=>0,
                "scheduleDate"=>"2019-05-02",
                "authorized"=>true,
                "failReason"=>null,
                "bankAccount"=>[
                    "bank"=>[
                        "code"=>"001",
                        "name"=>"Banco do Brasil"
                    ],
                    "accountName"=>"Conta Banco do Brasil",
                    "ownerName"=>"Marcelo Almeida",
                    "cpfCnpj"=>"52233424611",
                    "agency"=>"1263",
                    "agencyDigit"=>"3",
                    "account"=>"9999991",
                    "accountDigit"=>"1"
                    ],
                "transactionReceiptUrl"=>null
            ]
        ];
        VarDumper::dump($this->api->simulateWebhookTransfer($data)??[]);
    }

    public function clientLoan(){
        $value = intval($this->ask('Informe valor inteiro [ex. 145 para 1,45]'));
        $companyId = $this->ask('Informe companyId');
        VarDumper::dump($this->api->transferSellerToSubSeller($companyId,$value)??[]);
    }
}
