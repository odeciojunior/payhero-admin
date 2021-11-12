<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\User;
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
        $this->comment("======== MENU ASAAS ========");
        $this->comment("[1] Listar opções");
        $this->comment("[2] Company Transfers ");
        $this->comment("[3] Company Transfer");
        $this->comment("[4] Company Balance");
        $this->comment("[5] Cloudfox Balance");
        $this->comment("[6] Company Anticipation");
        $this->comment("[7] Company Anticipations");
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
            case 0:
                exit;
            break;
            default:
                $this->comment('Opção inválida');
            break;
        }
    }

    public function getTransfers()
    {
        $companyId  = $this->ask('Informe CompanyId');    
        VarDumper::dump($this->api->getTransfersAsaas($companyId));
    }

    public function getTransfer()
    {
        list($companyId,$transferId) = explode(',',$this->ask('Informe CompanyId,transferId'));
        
        VarDumper::dump($this->api->getTransferAsaas($companyId,$transferId));//3442,'c9ac9dc4-ac2a-4991-8c4d-013081b5f9bc'
    }

    public function getCompanyBalance(){
        $companyId  = $this->ask('Informe CompanyId');    
        VarDumper::dump($this->api->getCurrentBalance($companyId)??[]);
    }

    public function getCloudfoxBalance(){
        VarDumper::dump($this->api->getCurrentBalance()??[]);
    }

    public function getCompanyAntipation(){
        list($companyId,$anticipationId) = explode(',',$this->ask('Informe CompanyId,anticipationId'));
        
        VarDumper::dump($this->api->getAnticipationAsaas($companyId,$anticipationId));
    }

    public function getCompanyAntipations(){
        list($companyId) = $this->ask('Informe CompanyId');
        
        VarDumper::dump($this->api->getAnticipationsAsaas($companyId));
    }

}
