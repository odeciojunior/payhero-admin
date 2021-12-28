<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\AntifraudPostback;

class ProcessAntifraudPostbacksCommand extends Command
{
    protected $signature = 'antifraudpostbacks:process';

    protected $description = 'Verifica os postbacks do(s) antifraude(s)';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $postbacks = AntifraudPostback::where('processed_flag', false)
                ->with('sale')
                ->orderBy('id', 'asc')
                ->limit(100)
                ->get();
            $url = getenv('CHECKOUT_URL') . '/api/postback/process/antifraud';
            foreach ($postbacks as $postback) {
                $this->runCurl($url, 'POST', ['postback_id' => hashids_encode($postback->id)]);
            }
        } catch (Exception $e) {
            report($e);
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
