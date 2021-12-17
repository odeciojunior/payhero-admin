<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\AsaasBackofficeRequest;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Services\Gateways\CheckoutGateway;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class RegisterWebhookAsaas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'register-webhook-asaas';

    protected $description = 'Registra o webhook no asaas para todas as empresas pendentes';

    private int $gatewayId;
    private $api = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->gatewayId = foxutils()->isProduction() ? Gateway::ASAAS_PRODUCTION_ID:Gateway::ASAAS_SANDBOX_ID;
        $this->api = new CheckoutGateway($this->gatewayId);            
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $credentials = GatewaysCompaniesCredential::where('gateway_id',$this->gatewayId)->whereNotNull('gateway_api_key')
        ->where(function($qr){
            $qr->whereNull('has_webhook')->orWhere('has_webhook',0);
        })->get();

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, $credentials->count());
        $progress->start();

        foreach($credentials as $credential){
            $this->registerWebhook($credential);
            $progress->advance();
        }
        $progress->finish();
    }

    public function registerWebhook(GatewaysCompaniesCredential $credential){
        try{

            $response = $this->api->registerWebhookTransferAsaas($credential->company_id);            
            if($response->status =='success'){
                $credential->update(['has_webhook' => 1]);
            }
        }
        catch(Exception $ex) {
            Log::info($ex->getMessage());
            $credential->update(['has_webhook' => 0]);
        }
    }

}
