<?php

namespace Modules\Core\Services;

use Exception;
use Carbon\Carbon;
use Modules\Core\Entities\CurrencyQuotation;
use Modules\Core\Exceptions\Services\ServiceException;

/**
 * Class BankService
 * @package Modules\Core\Services
 */
class CurrencyQuotationService
{
    const apiUsd = 'https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoDolarDia(dataCotacao=@dataCotacao)?&$top=100&$format=json&@dataCotacao=';

    /**
     * @return mixed
     */
    private function getUsdQuotation()
    {
        $date = Carbon::yesterday()->format('m-d-Y');
        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, self::apiUsd . "'.$date.'");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        //execute post
        $response = curl_exec($ch);

        //close connection
        curl_close($ch);

        $quotationResponse = json_decode($response);

        if (empty($quotationResponse->value)) {
            //sem cotacao
            $quotation = 0;
        } else {
            $quotation = number_format(current($quotationResponse->value)->cotacaoCompra, 2, '', '');
        }

        $result['http_response'] = $response;
        $result['quotation']     = $quotation;

        return $result;
    }

    /**
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function updateQuotations()
    {
        $currencyQuotationModel = new CurrencyQuotation();

        $usdQuotation = $this->getUsdQuotation();

        $usdCurrencyQuotation = $currencyQuotationModel->create([
                                                                    'currency_type' => $currencyQuotationModel->present()
                                                                                                              ->getCurrencyType('USD'),
                                                                    'currency'      => 'USD',
                                                                    'http_response' => $usdQuotation['http_response'],
                                                                    'value'         => $usdQuotation['quotation'],
                                                                ]);
    }

    /**
     * @return mixed
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function getLastUsdQuotation()
    {
        $currencyQuotationModel = new CurrencyQuotation();
        $currencyQuotationUsd   = $currencyQuotationModel->where('currency_type', $currencyQuotationModel->present()
                                                                                                         ->getCurrencyType('USD'))
                                                         ->latest('id')
                                                         ->first();

        return $currencyQuotationUsd;
    }
}
