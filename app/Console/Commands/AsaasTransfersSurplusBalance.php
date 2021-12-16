<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\CheckoutGateway;
use PhpParser\Node\Stmt\While_;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

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
        $companies = Company::whereHas('transactions', function($q) {
            $q->where('gateway_id' ,Gateway::ASAAS_PRODUCTION_ID)
            ->where('created_at', '>' , '2021-09');
        })
        ->with('user')
        ->get();
        
        $checkoutGateway = new CheckoutGateway($this->gatewayId);
        $asaasService = new AsaasService();

        $asaasBalance = 0;
        $companyBalance = 0;
        $amountTransfer = 0;
        $status = '';

        Log::info(
            str_pad("Company",15,' ',STR_PAD_RIGHT).
            str_pad("Asaas Bal.",15,' ',STR_PAD_RIGHT).
            str_pad("Company Bal.",15,' ',STR_PAD_RIGHT).
            str_pad("Balance",15,' ',STR_PAD_RIGHT).
            str_pad('Status',15,' ',STR_PAD_RIGHT).
            "Usuário"
        ); 
        $this->comment('processando...');
        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, count($companies));
        $progress->start();
    
        foreach($companies as $company)
        {
            $progress->advance();
            $asaasBalance = 0;
            $companyBalance = 0;
            $amountTransfer = 0;
            $status = 'Nothing';
            
            $response = $checkoutGateway->getCurrentBalance($company->id);
            if(!empty($response->status) && $response->status == 'success'){                
                $asaasBalance = $response->total_balance*100;
            }

            $pendingChargebacks = Transfer::whereDoesntHave('asaasTransfer',function($qr){
                $qr->where('status','DONE');
            })
            ->where('reason','chargedback')
            ->where('gateway_id',$this->gatewayId)
            ->where('company_id', $company->id)
            ->sum('value');

            $asaasBalance -= $pendingChargebacks;

            $asaasService->setCompany($company);
            $companyBalance = $asaasService->getAvailableBalance() + $asaasService->getPendingBalance();

            $pendingWtihdrawals = Withdrawal::where('company_id',$company->id)
            ->whereIn('status',[Withdrawal::STATUS_PENDING,Withdrawal::STATUS_IN_REVIEW])->sum('value');
            $companyBalance+= $pendingWtihdrawals;          

            if($asaasBalance > 0 && $asaasBalance > $companyBalance)
            {
                $amountTransfer = intval($companyBalance > 0 ? $asaasBalance - $companyBalance : $asaasBalance);
                
                $response = $checkoutGateway->transferSubSellerToSeller(
                    $company->id, $amountTransfer
                );

                $status = 'Done';
                if(empty($response) || empty($response->status) || $response->status=='error'){                
                    $status = 'Error';
                }
            }  

            Log::info(
                str_pad($company->id,15,' ',STR_PAD_RIGHT).
                str_pad($asaasBalance,15,' ',STR_PAD_RIGHT).
                str_pad($companyBalance,15,' ',STR_PAD_RIGHT).
                str_pad($amountTransfer,15,' ',STR_PAD_RIGHT).
                str_pad($status ,15,' ',STR_PAD_RIGHT).
                $company->user->name 
            );    
        }

        $progress->finish();
    }
}
