<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\BiometryPostback;
use Modules\Core\Entities\GatewayPostback;

class ProcessBiometryPostbacksCommand extends Command
{
    protected $signature = "biometrypostbacks:process";

    protected $description = "Checks biometrics postbacks";

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
            $postbacks = BiometryPostback::select("id")
                ->where("processed_flag", false)
                ->orderBy("id", "desc")
                ->limit(30)
                ->get()
                ->toArray();

            $url = getenv("ACCOUNT_BACK_URL") . "/api/postback/process/unico/check";
            $result = $this->runCurl($url, "POST", ["postback_id" => hashids_encode(12)]);
            dd($url);
            // foreach ($postbacks as $postback) {
            //     $result = $this->runCurl($url, "POST", ["postback_id" => hashids_encode($postback["id"])]);
            //     dd($result);
            // }

            // $this->processPostBackGetnet();
            // $this->processPostbackAsaas();
            // $this->processPostbackSafe2pay();
        } catch (Exception $e) {
            dd($e);
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
                ->where("gateway_id", 21)
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

    private function runCurl($url, $method = "GET", $data = null)
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
            $result = json_decode($result);
            return $result;
        } catch (Exception $ex) {
            dd($ex);
            throw $ex;
        }
    }
}


// curl http://dev.accounts-api.com:7030
