<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPostbacks implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $url;
    public $method;
    public $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url, $method = "GET", $data = null)
    {
        $this->url = $url;
        $this->method = $method;
        $this->data = $data;
    }

    public function tags()
    {
        return ["process-postbacks"];
    }

    public function handle()
    {
        try {
            $this->runCurl($this->url, $this->method, $this->data);
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function runCurl($url, $method = "GET", $data = null): void
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
