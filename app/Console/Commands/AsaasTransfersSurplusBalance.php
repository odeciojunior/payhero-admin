<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\CheckoutGateway;

class AsaasTransfersSurplusBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asaas:transfers-surplus-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $gatewayId = 0;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->gatewayId = foxutils()->isProduction() ? Gateway::ASAAS_PRODUCTION_ID : Gateway::ASAAS_SANDBOX_ID;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $companies = Company::where('bank_document_status',Company::DOCUMENT_STATUS_APPROVED)
        ->where('address_document_status',Company::DOCUMENT_STATUS_APPROVED)
        ->where('contract_document_status',Company::DOCUMENT_STATUS_APPROVED)
        ->get();

        dd(count($companies));

        $checkoutGateway = new CheckoutGateway($this->gatewayId);
        $asaasService = new AsaasService();

        $asaasBalance = 0;
        $companyBalance = 0;
        $amountTransfer = 0;
        $status = '';

        $this->line(
            str_pad("Company",15,' ',STR_PAD_RIGHT).
            str_pad("Asaas Bal.",15,' ',STR_PAD_RIGHT).
            str_pad("Company Bal.",15,' ',STR_PAD_RIGHT).
            str_pad("Balance",15,' ',STR_PAD_RIGHT).
            'Status'
        ); 
        
        foreach($companies as $company)
        {
            $asaasBalance = 0;
            $companyBalance = 0;
            $amountTransfer = 0;
            $status = 'Nothing';
            
            $response = $checkoutGateway->getCurrentBalance($company->id);
            if(!empty($response->status) && $response->status == 'success'){                
                $asaasBalance = $response->total_balance*100;
            }

            $asaasService->setCompany($company);
            $companyBalance = $asaasService->getAvailableBalance() + $asaasService->getPendingBalance();

            if($asaasBalance > 0 && $asaasBalance > $companyBalance)
            {
                $amountTransfer = $companyBalance > 0 ? $asaasBalance - $companyBalance : $asaasBalance;
                
                $response = $checkoutGateway->transferSubSellerToSeller(
                    $company->id, $amountTransfer
                );

                $status = 'Done';
                if(empty($response) || empty($response->status) || $response->status=='error'){                
                    $status = 'Error';
                }
            }  

            $this->line(
                str_pad($company->id,15,' ',STR_PAD_RIGHT).
                str_pad($asaasBalance,15,' ',STR_PAD_RIGHT).
                str_pad($companyBalance,15,' ',STR_PAD_RIGHT).
                str_pad($amountTransfer,15,' ',STR_PAD_RIGHT).
                $status
            );    
        }
    }
}
