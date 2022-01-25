<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\ApiToken;
use Modules\Core\Entities\CheckoutApiPostback;
use Modules\Core\Entities\Sale;
use Vinkla\Hashids\Facades\Hashids;

class SendPostbackApiBoletoPaid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postback-boleto-paid:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sales = Sale::where('api_flag', 1)
        ->where('payment_method', Sale::BOLETO_PAYMENT)
        ->where('status', Sale::STATUS_APPROVED)
        ->get();

        foreach($sales as $sale) {
            $data = [
                'sale_id'   => Hashids::connection('sale_id')->encode($sale->id),
                'status'    => 'paid'
            ];

            $apiToken = ApiToken::where('user_id', $sale->owner_id)->first();
            if(!empty($apiToken->postback) && $apiToken->integration_type_enum == 4) {
                $httpCode = $this->execCurl($data, $apiToken);

                if($httpCode <> 200) {
                    $this->line('Enviado ', $sale->id);
                } else {
                    $this->line('Erro ', $sale->id);
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
