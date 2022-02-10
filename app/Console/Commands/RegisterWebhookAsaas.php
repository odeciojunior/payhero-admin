<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Services\Gateways\CheckoutGateway;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Support\Facades\Log;

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

        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {

            $credentials = GatewaysCompaniesCredential::where('gateway_id',$this->gatewayId)->whereNotNull('gateway_api_key')
                ->where(function($qr){
                    $qr->whereNull('has_transfers_webhook')->orWhere('has_transfers_webhook',0)
                        ->orWhereNull('has_charges_webhook')->orWhere('has_charges_webhook',0);
                })->get();

            $output = new ConsoleOutput();
            $progress = new ProgressBar($output, $credentials->count());
            $progress->start();

            foreach($credentials as $credential){
                $this->registerTransferWebhook($credential);
                $this->registerChargeWebhook($credential);
                $progress->advance();
            }
            $progress->finish();

        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }

    public function registerTransferWebhook(GatewaysCompaniesCredential $credential){
        try{

            if($credential->has_transfers_webhook <> 1){
                $response = $this->api->registerTransfersWebhookAsaas($credential->company_id);
                if($response->status =='success'){
                    $credential->update(['has_transfers_webhook' => 1]);
                }
            }
        }
        catch(Exception $ex) {
            $credential->update(['has_transfers_webhook' => 0]);
        }
    }

    public function registerChargeWebhook(GatewaysCompaniesCredential $credential)
    {
        try{
            if($credential->has_charges_webhook <> 1){
                $response = $this->api->registerChargesWebhookAsaas($credential->company_id);
                if($response->status =='success'){
                    $credential->update(['has_charges_webhook' => 1]);
                }
            }
        }
        catch(Exception $ex) {
            $credential->update(['has_charges_webhook' => 0]);
        }
    }

}
