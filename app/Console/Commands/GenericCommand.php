<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\ApiToken;
use Modules\Core\Entities\CheckoutApiPostback;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $apiPostbacks = CheckoutApiPostback::where('company_id', 6386)->where('user_id', 6679)->orderBy('id', 'desc')->get();
        foreach($apiPostbacks as $apiPostback)
        {
            $status = json_decode($apiPostback->response)->status;
            if ($status == 403) {
                $dataDecode = json_decode($apiPostback->sent_data);

                $data = [
                    'sale_id'   => $dataDecode->sale->sale_id,
                    'status'    => $dataDecode->data->status
                ];

                $apiToken = ApiToken::find(828);
                if(!empty($apiToken->postback) && $apiToken->integration_type_enum == 4) {
                    $httpCode = $this->execCurl($data, $apiToken);
                    $this->line($httpCode);

                    if($httpCode <> 200) {
                        throw new Exception("Não foi possivel enviar dados para {$apiToken->postback}");
                    }
                }
            }
        }
    }

    public function execCurl($data, $apiToken) {
        $curl = curl_init();

        $url = $apiToken->postback;

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        $result = json_decode($response, true);
        $companyId = $apiToken->company_id;
        $userId = $apiToken->user_id;

        CheckoutApiPostback::create(
            [
                'company_id' => $companyId,
                'user_id' => $userId,
                'sent_data' => json_encode(
                    [
                        'url' => $url,
                        'data' => $data
                    ]
                ),
                'response' => json_encode(
                    [
                        'result' => $result,
                        'status' => $httpCode
                    ]
                )
            ]
        );

        return $httpCode;
    }
}
