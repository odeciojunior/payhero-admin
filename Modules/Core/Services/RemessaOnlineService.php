<?php

namespace Modules\Core\Services;

use GuzzleHttp\Client;
use Illuminate\Http\Response;

class RemessaOnlineService
{

    /**
     * @var string
     */
    const DOLAR_QUOTATION_URL_SOURCE = "https://www.remessaonline.com.br/api/current-quotation/USD/COM";

    /**
     * @var string
     */
    const EURO_QUOTATION_URL_SOURCE = "https://www.remessaonline.com.br/api/current-quotation/EUR/COM";


    /**
     * @return float|null
     */
    public function getCurrentQuotation($currency){

        try{

            $client = new Client();

            $urlSource = $this->getUrlSource($currency);

            if(empty($urlSource)){
                return null;
            }

            $response = json_decode($client->get($urlSource)->getBody()->getContents());

            return $response->value;
        }
        catch(Exception $e){
            return null;
        }

    }


    /**
     * @return string
     */
    private function getUrlSource($currency = null){

        switch($currency){
            case 'dolar':
                return self::DOLAR_QUOTATION_URL_SOURCE;
            case 'euro':
                return self::EURO_QUOTATION_URL_SOURCE;
            default:
                return null;
        }
    }

}
