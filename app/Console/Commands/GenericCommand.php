<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;

class GenericCommand extends Command
{
    protected $signature = "generic {name?}";
    protected $description = "Command description";
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $url = "https://ws.pag360.app.br/hubPay/api/v1/feeOpts";
        $token = "b5d701e1-94ba-4fee-a947-ab65088f5ddc";
        $data = [
            "pan" => "417537",
            "desiredNetAmount" => 1000.0,
        ];

        $payload = json_encode($data);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "Auth-API-Pag360: Bearer $token"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo "Curl error: " . curl_error($ch);
        } else {
            echo $response;
        }

        curl_close($ch);

        dump(json_decode($response));
    }
}
