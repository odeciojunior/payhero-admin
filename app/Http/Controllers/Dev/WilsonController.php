<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\CheckoutService;
use Modules\Core\Services\FoxUtils;
use PHPHtmlParser\Dom;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class WilsonController
 * @package App\Http\Controllers\Dev
 */
class WilsonController extends Controller
{
    public function wilsonFunction(Request $request)
    {
        $saleIds         = [
            //            103304, - ok
            //            103302, - ok
            //            103300, - ok
            //            103299, - ok
            //            103293, - ok
            //            103289, - ok
            //            103285, - ok
            //            103283, - ok
            //            103282, - ok
            //            103281, - ok
            //            103279, - ok
            //            103278, - ok
            //            103277, - ok
            //            103276, - ok
            //            103273, - ok
            //            103272, - ok
            //            103271, - ok
            //            103270, - ok
            //            103269, - ok
            //            103268, - ok
            //            103265, - ok
            //            103264, - ok
            //            103262, - ok
            //            103261, - ok
            //            103258, - ok
            //            103255, - ok
            //            103254, - ok
            //            103253, - ok
            //            103250, - ok
//            103249, - ok
//            103248, - ok
//            103247,
//            103246,
//            103245,
//            103243,
//            103242,
//            103241,
//            103240,
//            103239,
            //            103238,
            //            103237,
            //            103236,
            //            103234,
            //            103230,
            //            103229,
            //            103228,
            //            103224,
            //            103223,
            //            103221,
            //            103220,
            //            103219,
            //            103218,
            //            103215,
            //            103214,
            //            103213,
            //            103211,
            //            103209,
            //            103208,
            //            103205,
            //            103202,
            //            103200,
            //            103199,
            //            103197,
            //            103195,
            //            103194,
            //            103193,
            //            103192,
            //            103189,
            //            103183,
            //            103181,
            //            103178,
            //            103174,
            //            103171,
            //            103169,
            //            103168,
            //            103167,
            //            103166,
            //            103165,
            //            103163,
            //            103162,
            //            103160,
            //            103157,
            //            103156,
            //            103155,
            //            103154,
            //            103153,
            //            103152,
            //            103151,
            //            103149,
            //            103148,
            //            103143,
            //            103142,
            //            103140,
            //            103136,
            //            103131,
            //            103130,
            //            103129,
            //            103127,
            //            103126,
            //            103125,
            //            103121,
            //            103120,
            //            103119,
            //            103117,
            //            103115,
            //            103114,
        ];
//        $checkoutService = new CheckoutService();
//
//        foreach ($saleIds as $saleId) {
//            $sale       = Sale::find($saleId);
//            $saleIdHash = Hashids::connection('sale_id')->encode($sale->id);
//
//            $totalPaidValue = preg_replace("/[^0-9]/", "", $sale->sub_total);
//            $shippingPrice  = preg_replace("/[^0-9]/", "", $sale->shipment_value);
//            dump($saleIdHash, ($totalPaidValue + $shippingPrice));
//            $checkoutService->regenerateBillet($saleIdHash, ($totalPaidValue + $shippingPrice), $sale->boleto_due_date);
//            dump($saleIdHash . '- OK');
//        }
        dd('oooook');
        //        //        $salesArray = [
        //        //            '8gmDNvGB',
        //        //            '436ELygQ',
        //        //            'kZ7K4NZ0',
        //        //            'jZD5KWGp',
        //        //            '83EB0lZP',
        //        //            '0glxB6gV',
        //        //            //            'aGnjnjGw', ?????
        //        //            'PZvkMYGm',
        //        //            '0ZOlXXGE',
        //        //            'R3A7X9Gd',
        //        //        ];
        //        //
        //        //        $salesId = [];
        //        //
        //        //        foreach ($salesArray as $saleCode) {
        //        //            $salesId[] = Hashids::connection('sale_id')->decode($saleCode)[0];
        //        //        }
        //        //
        //        //        dd($salesId);
        //        if (($request->cancelPayment ?? false) == true && isset($request->saleId)) {
        //            $saleModel       = new Sale();
        //            $sale            = $saleModel->with('transactions')->find($request->saleId);
        //            $checkoutService = new CheckoutService();
        //            $refundAmount    = $sale->original_total_paid_value;
        //            $checkoutService->cancelPayment($sale, $refundAmount);
        //            $sale = $saleModel->with('transactions')->find($request->saleId);
        //            dd($sale);
        //        }
        //        $body  = new Dom();
        //        $body2 = new Dom();
        //        $body->loadFromUrl('https://api-boleto-production.s3.amazonaws.com/72664906b4444f0fa900098844baf84b/43e2543afd5c45e7b219a26c36551bdf/5dd7fb3c55065a15068e1379.html');
        //        $body->loadStr($body->find('table')->find('td')->toArray()[1]->innerHtml());
        //        $body->loadStr($body->find('tr')->toArray()[4]->innerHtml());
        //        $body2->loadStr($body->find('tr')->toArray()[8]->innerHtml());
        //
        //        $teste = [
        //            'Pagador/Cpf/Cnpj'             => trim(strip_tags($body->find('tr')
        //                                                                    ->toArray()[5]->innerHtml()), ' \t\n\r'),
        //            'Endereço'                     => trim(strip_tags($body->find('tr')
        //                                                                    ->toArray()[6]->innerHtml()), ' \t\n\r'),
        //            'NossoNumero'                  => trim(strip_tags($body->find('tr')
        //                                                                    ->toArray()[9]->find('td')[0]->innerHtml()), ' \t\n\r'),
        //            'NumeroDoDocumento'            => trim(strip_tags($body->find('tr')
        //                                                                    ->toArray()[9]->find('td')[1]->innerHtml()), ' \t\n\r'),
        //            'DataDeVencimento'             => trim(strip_tags($body->find('tr')
        //                                                                    ->toArray()[9]->find('td')[2]->innerHtml()), ' \t\n\r'),
        //            'ValorDoDocumento'             => trim(strip_tags($body->find('tr')
        //                                                                    ->toArray()[9]->find('td')[4]->innerHtml()), ' \t\n\r'),
        //            'ValorCobrado'                 => trim(strip_tags($body->find('tr')
        //                                                                    ->toArray()[9]->find('td')[5]->innerHtml()), ' \t\n\r'),
        //            'NomeDoBeneficiario1'           => trim(strip_tags($body->find('tr')->toArray()[11]->find('td')
        //                                                                                               ->innerHtml()), ' \t\n\r'),
        //            'Agencia/CodigoDoBeneficiario' => trim(strip_tags($body->find('tr')->toArray()[15]->find('td')
        //                                                                                               ->innerHtml()), ' \t\n\r'),
        //        ];
        //        dd($teste);
        //        $htmlBoleto = strip_tags($body);
        //
        //        $arrayBoleto = explode('~', $this->teste($htmlBoleto));
        //        $keyAgencia  = array_search('Agência / Código do Beneficiário', $arrayBoleto);
        //        //        dd(array_search('Agência / Código do Beneficiário', $arrayBoleto));
        //        dd($arrayBoleto);
        //        dd($arrayBoleto[$keyAgencia + 2]);
        //        $htmlBoleto = str_replace('- Zoop Brasil', '', $htmlBoleto);
        //        foreach ($dom->load($htmlBoleto)->find('tbody') as $key => $t) {
        //            dump($key . $t->innerHtml());
        //            if ($key == 3) {
        //                $t->addChild('img');
        //            }
        //        }
        //
        //        return $htmlBoleto;
        //        dd($dom->load($htmlBoleto)->find('tr'));
        //        dd('asdasdasd');
        //        $s = $x;
        //        //        $sale        = Sale::where('id', 11012)->first();
        //        //        $saleService = new SaleService();
        //        //        $response    = (object) [
        //        //            'status'         => 'success',
        //        //            'message'        => 'Venda cancelada com sucesso!',
        //        //            'status_gateway' => 'successed',
        //        //            'status_sale'    => 'paid',
        //        //            'response'       => [],
        //        //        ];
        //        //
        //        //        $saleService->updateSaleRefunded($sale, 300, $response);
        //        //        dd('deu bom ?');
    }

    public function teste($string)
    {
        $string = str_replace('&nbsp;', '', $string);
        $string = str_replace('                                                                               ', '  ', $string);
        $string = str_replace('                                                                              ', '  ', $string);
        $string = str_replace('                                                                             ', '  ', $string);
        $string = str_replace('                                                                            ', '  ', $string);
        $string = str_replace('                                                                           ', '  ', $string);
        $string = str_replace('                                                                          ', '  ', $string);
        $string = str_replace('                                                                         ', '  ', $string);
        $string = str_replace('                                                                        ', '  ', $string);
        $string = str_replace('                                                                       ', '  ', $string);
        $string = str_replace('                                                                      ', '  ', $string);
        $string = str_replace('                                                                     ', '  ', $string);
        $string = str_replace('                                                                    ', '  ', $string);
        $string = str_replace('                                                                   ', '  ', $string);
        $string = str_replace('                                                                  ', '  ', $string);
        $string = str_replace('                                                                 ', '  ', $string);
        $string = str_replace('                                                                ', '  ', $string);
        $string = str_replace('                                                               ', '  ', $string);
        $string = str_replace('                                                              ', '  ', $string);
        $string = str_replace('                                                             ', '  ', $string);
        $string = str_replace('                                                            ', '  ', $string);
        $string = str_replace('                                                           ', '  ', $string);
        $string = str_replace('                                                          ', '  ', $string);
        $string = str_replace('                                                         ', '  ', $string);
        $string = str_replace('                                                        ', '  ', $string);
        $string = str_replace('                                                       ', '  ', $string);
        $string = str_replace('                                                      ', '  ', $string);
        $string = str_replace('                                                     ', '  ', $string);
        $string = str_replace('                                                    ', '  ', $string);
        $string = str_replace('                                                   ', '  ', $string);
        $string = str_replace('                                                  ', '  ', $string);
        $string = str_replace('                                                 ', '  ', $string);
        $string = str_replace('                                                ', '  ', $string);
        $string = str_replace('                                               ', '  ', $string);
        $string = str_replace('                                              ', '  ', $string);
        $string = str_replace('                                             ', '  ', $string);
        $string = str_replace('                                            ', '  ', $string);
        $string = str_replace('                                           ', '  ', $string);
        $string = str_replace('                                          ', '  ', $string);
        $string = str_replace('                                         ', '  ', $string);
        $string = str_replace('                                       ', '  ', $string);
        $string = str_replace('                                      ', '  ', $string);
        $string = str_replace('                                     ', '  ', $string);
        $string = str_replace('                                    ', '  ', $string);
        $string = str_replace('                                   ', '  ', $string);
        $string = str_replace('                                  ', '  ', $string);
        $string = str_replace('                                 ', '  ', $string);
        $string = str_replace('                                ', '  ', $string);
        $string = str_replace('                               ', '  ', $string);
        $string = str_replace('                              ', '  ', $string);
        $string = str_replace('                             ', '  ', $string);
        $string = str_replace('                            ', '  ', $string);
        $string = str_replace('                           ', '  ', $string);
        $string = str_replace('                          ', '  ', $string);
        $string = str_replace('                         ', '  ', $string);
        $string = str_replace('                        ', '  ', $string);
        $string = str_replace('                       ', '  ', $string);
        $string = str_replace('                      ', '  ', $string);
        $string = str_replace('                     ', '  ', $string);
        $string = str_replace('                    ', '  ', $string);
        $string = str_replace('                   ', '  ', $string);
        $string = str_replace('                  ', '  ', $string);
        $string = str_replace('                 ', '  ', $string);
        $string = str_replace('                ', '  ', $string);
        $string = str_replace('               ', '  ', $string);
        $string = str_replace('              ', '  ', $string);
        $string = str_replace('            ', '  ', $string);
        $string = str_replace('           ', '  ', $string);
        $string = str_replace('          ', '  ', $string);
        $string = str_replace('         ', '  ', $string);
        $string = str_replace('        ', '  ', $string);
        $string = str_replace('       ', '  ', $string);
        $string = str_replace('      ', '  ', $string);
        $string = str_replace('     ', '  ', $string);
        $string = str_replace('    ', '  ', $string);
        $string = str_replace('   ', '  ', $string);
        $string = str_replace('  ', '~', $string);

        return $string;
    }
}


