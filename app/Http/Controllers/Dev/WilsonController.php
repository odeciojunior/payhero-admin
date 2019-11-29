<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use PHPHtmlParser\Dom;

/**
 * Class WilsonController
 * @package App\Http\Controllers\Dev
 */
class WilsonController extends Controller
{
    public function wilsonFunction()
    {
        $body  = new Dom();
        $body2 = new Dom();
        $body->loadFromUrl('https://api-boleto-production.s3.amazonaws.com/72664906b4444f0fa900098844baf84b/43e2543afd5c45e7b219a26c36551bdf/5dd7fb3c55065a15068e1379.html');
        $body->loadStr($body->find('table')->find('td')->toArray()[1]->innerHtml());
        $body->loadStr($body->find('tr')->toArray()[4]->innerHtml());
        $body2->loadStr($body->find('tr')->toArray()[8]->innerHtml());

        $teste = [
            'Pagador/Cpf/Cnpj'             => trim(strip_tags($body->find('tr')
                                                                    ->toArray()[5]->innerHtml()), ' \t\n\r'),
            'Endereço'                     => trim(strip_tags($body->find('tr')
                                                                    ->toArray()[6]->innerHtml()), ' \t\n\r'),
            'NossoNumero'                  => trim(strip_tags($body->find('tr')
                                                                    ->toArray()[9]->find('td')[0]->innerHtml()), ' \t\n\r'),
            'NumeroDoDocumento'            => trim(strip_tags($body->find('tr')
                                                                    ->toArray()[9]->find('td')[1]->innerHtml()), ' \t\n\r'),
            'DataDeVencimento'             => trim(strip_tags($body->find('tr')
                                                                    ->toArray()[9]->find('td')[2]->innerHtml()), ' \t\n\r'),
            'ValorDoDocumento'             => trim(strip_tags($body->find('tr')
                                                                    ->toArray()[9]->find('td')[4]->innerHtml()), ' \t\n\r'),
            'ValorCobrado'                 => trim(strip_tags($body->find('tr')
                                                                    ->toArray()[9]->find('td')[5]->innerHtml()), ' \t\n\r'),
            'NomeDoBeneficiario1'           => trim(strip_tags($body->find('tr')->toArray()[11]->find('td')
                                                                                               ->innerHtml()), ' \t\n\r'),
            'Agencia/CodigoDoBeneficiario' => trim(strip_tags($body->find('tr')->toArray()[15]->find('td')
                                                                                               ->innerHtml()), ' \t\n\r'),
        ];
        dd($teste);
        $htmlBoleto = strip_tags($body);

        $arrayBoleto = explode('~', $this->teste($htmlBoleto));
        $keyAgencia  = array_search('Agência / Código do Beneficiário', $arrayBoleto);
        //        dd(array_search('Agência / Código do Beneficiário', $arrayBoleto));
        dd($arrayBoleto);
        dd($arrayBoleto[$keyAgencia + 2]);
        $htmlBoleto = str_replace('- Zoop Brasil', '', $htmlBoleto);
        foreach ($dom->load($htmlBoleto)->find('tbody') as $key => $t) {
            dump($key . $t->innerHtml());
            if ($key == 3) {
                $t->addChild('img');
            }
        }

        return $htmlBoleto;
        dd($dom->load($htmlBoleto)->find('tr'));
        dd('asdasdasd');
        $s = $x;
        //        $sale        = Sale::where('id', 11012)->first();
        //        $saleService = new SaleService();
        //        $response    = (object) [
        //            'status'         => 'success',
        //            'message'        => 'Venda cancelada com sucesso!',
        //            'status_gateway' => 'successed',
        //            'status_sale'    => 'paid',
        //            'response'       => [],
        //        ];
        //
        //        $saleService->updateSaleRefunded($sale, 300, $response);
        //        dd('deu bom ?');
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


