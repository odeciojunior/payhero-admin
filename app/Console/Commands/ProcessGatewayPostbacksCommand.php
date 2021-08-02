<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\GatewayPostback;

class ProcessGatewayPostbacksCommand extends Command
{
    protected $signature = 'gatewaypostbacks:process';

    protected $description = 'Verifica os postbacks dos gateways';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->processPostBackGetnet();
        $this->processPostbackAsaas();
    }

    private function processPostBackGetnet()
    {
        $postbacks = GatewayPostback::where('processed_flag', false)
            ->where('gateway_id', 15)
            ->orderBy('id', 'desc')
            ->limit(100)
            ->get();
        $url = getenv('CHECKOUT_URL') . '/api/postback/process/getnet';

        foreach ($postbacks as $postback) {
            $this->runCurl($url, 'POST', ['postback_id' => hashids_encode($postback->id)]);
        }
    }

    private function processPostbackAsaas()
    {
        $postbacks = GatewayPostback::where('processed_flag', false)
            ->where('gateway_id', 8)
            ->orderBy('id', 'asc')
            ->limit(100)
            ->get();
        $url = getenv('CHECKOUT_URL') . '/api/postback/process/asaas';

        foreach ($postbacks as $postback) {
            $this->runCurl($url, 'POST', ['postback_id' => hashids_encode($postback->id)]);
        }
    }

    private function runCurl($url, $method = 'GET', $data = null): void
    {
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            if ($method == "POST") {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, foxutils()->getHeadersInternalAPI());
            $result = curl_exec($ch);
            json_decode($result);
            return;
        } catch (Exception $ex) {
            report($ex);
            throw $ex;
        }
    }
}
