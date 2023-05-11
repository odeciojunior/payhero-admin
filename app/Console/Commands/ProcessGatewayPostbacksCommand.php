<?php

namespace App\Console\Commands;

//use App\Jobs\ProcessPostbacks;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\GatewayPostback;

class ProcessGatewayPostbacksCommand extends Command
{
    protected $signature = "gatewaypostbacks:process";

    protected $description = "Verifica os postbacks dos gateways GETNET, ASAAS";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    public function handle()
    {
        try {
            // $this->processPostBackGetnet();
            // $this->processPostbackAsaas();
            $this->processPostbackSafe2pay();
            $this->processPostbackIugu();
        } catch (Exception $e) {
            report($e);
        }
    }

    private function processPostBackGetnet()
    {
        try {
            $postbacks = GatewayPostback::select("id")
                ->where("processed_flag", false)
                ->where("gateway_id", 15)
                ->orderBy("id", "desc")
                ->limit(100)
                ->get()
                ->toArray();

            $url = getenv("CHECKOUT_URL") . "/api/postback/process/getnet";

            foreach ($postbacks as $postback) {
                $this->runCurl($url, "POST", ["postback_id" => hashids_encode($postback["id"])]);
            }
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * @throws Exception
     */
    private function processPostbackAsaas()
    {
        try {
            $postbacks = GatewayPostback::select("id")
                ->where("processed_flag", false)
                ->where("gateway_id", 8)
                ->orderBy("id", "asc")
                ->limit(100)
                ->get()
                ->toArray();

            $url = getenv("CHECKOUT_URL") . "/api/postback/process/asaas";

            foreach ($postbacks as $postback) {
                $this->runCurl($url, "POST", ["postback_id" => hashids_encode($postback["id"])]);
            }
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * @throws Exception
     */
    private function processPostbackSafe2pay()
    {
        try {
            $postbacks = GatewayPostback::select("id")
                ->where("processed_flag", false)
                ->where("gateway_id", 3)
                ->orderBy("id", "asc")
                ->limit(100)
                ->get()
                ->toArray();

            $url = getenv("CHECKOUT_URL") . "/api/postback/process/safe2pay";

            foreach ($postbacks as $postback) {
                $this->runCurl($url, "POST", ["postback_id" => hashids_encode($postback["id"])]);
            }
        } catch (Exception $e) {
            report($e);
        }
    }

    private function processPostbackIugu()
    {
        try {
            $postbacks = GatewayPostback::select("id")
                ->where("processed_flag", false)
                ->where("gateway_id", 7)
                ->orderBy("id", "asc")
                ->limit(100)
                ->get()
                ->toArray();

            $url = getenv("CHECKOUT_URL") . "/api/postback/process/iugu";

            foreach ($postbacks as $postback) {
                $this->runCurl($url, "POST", ["postback_id" => hashids_encode($postback["id"])]);
            }
        } catch (Exception $e) {
            report($e);
        }
    }

    private function runCurl($url, $method = "GET", $data = null): void
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
