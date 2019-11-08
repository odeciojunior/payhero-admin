<?php

namespace Modules\Core\Services;

use App\Jobs\SendNotazzInvoiceJob;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\NotazzIntegration;
use Modules\Core\Entities\NotazzInvoice;
use Modules\Core\Entities\NotazzSentHistory;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Notifications\Notifications\RetroactiveNotazzNotification;
use Vinkla\Hashids\Facades\Hashids;

class NotazzService
{
    /**
     * @var string
     * @description name of the column in user_notifications table to check if it will send
     */
    private $userNotification = "notazz";
    /**
     * @var string
     */
    const NotazzUrlApi = 'https://app.notazz.com/api';

    /**
     * @param $projectId
     * @return bool
     */
    public function getNotazzApi($projectId)
    {
        $projectModel = new Project();

        $project = $projectModel->with(['notazzIntegration'])->find($projectId);

        if ($project) {
            return $project->notazzIntegration->token_api;
        } else {
            return false;
        }
    }

    /**
     * @param $fields
     * @return mixed
     */
    function sendRequest($fields)
    {
        $fields        = ["fields" => $fields];
        $fields_string = '';

        //url-ify the data for the POST
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }

        rtrim($fields_string, '&');

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, 'https://app.notazz.com/api');
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        //execute post
        $response = curl_exec($ch);

        //close connection
        curl_close($ch);

        //Convertendo json para array
        $pos = strpos($response, '{');

        return (json_decode(substr($response, $pos), false));
    }

    /**
     * @param $notazzInvoiceId
     * @return bool|mixed
     */
    public function sendNfse($notazzInvoiceId)
    {
        $notazzInvoiceModel       = new NotazzInvoice();
        $notazzSentHistoryModel   = new NotazzSentHistory();
        $saleModel                = new Sale();
        $productPlanModel         = new ProductPlan();
        $currencyQuotationService = new CurrencyQuotationService();

        $notazzInvoice = $notazzInvoiceModel->with([
                                                       'sale',
                                                       'sale.client',
                                                       'sale.delivery',
                                                       'sale.shipping',
                                                       'sale.plansSales.plan.products',
                                                       'sale.project.notazzIntegration',
                                                   ])->find($notazzInvoiceId);

        $sale = $notazzInvoice->sale;
        if ($sale) {
            //venda encontrada

            $sale = $saleModel->with(['plansSales'])->find($sale->id);

            $productsSale = collect();
            /** @var PlanSale $planSale */
            foreach ($sale->plansSales as $planSale) {
                /** @var ProductPlan $productPlan */
                foreach ($planSale->plan->productsPlans as $productPlan) {

                    $product = $productPlan->product()->first();

                    if (!empty($productPlan->cost)) {
                        //pega os valores de productplan
                        $product['product_cost']       = preg_replace("/[^0-9]/", "", $productPlan->cost);
                        $product['product_cost']       = (is_numeric($product['product_cost'])) ? $product['product_cost'] : 0;
                        $product['currency_type_enum'] = $productPlan->currency_type_enum;
                    } else {
                        //pega os valores de produto
                        if (!empty($product->cost)) {
                            $product['product_cost'] = preg_replace("/[^0-9]/", "", $product->cost);
                            $product['product_cost'] = (is_numeric($product['product_cost'])) ? $product['product_cost'] : 0;
                        } else {
                            $product['product_cost'] = 0;
                        }

                        $product['currency_type_enum'] = $product->currency_type_enum ?? 1;
                    }

                    $product['product_amount'] = $productPlan->amount ?? 1;

                    $productsSale->add($product);
                }
            }

            $products = $productsSale;

            if ($products) {
                $costTotal = 0;
                foreach ($products as $product) {

                    if ($product['currency_type_enum'] == $productPlanModel->present()->getCurrency('USD')) {
                        //moeda USD
                        $lastUsdQuotation        = $currencyQuotationService->getLastUsdQuotation();
                        $product['product_cost'] = (int) ($product['product_cost'] * ($lastUsdQuotation->value / 100));
                    }

                    $costTotal += (int) ($product['product_cost'] * $product['product_amount']);
                }

                $shippingCost = preg_replace("/[^0-9]/", "", $sale->shipment_value);

                $subTotal  = preg_replace("/[^0-9]/", "", $sale->sub_total);
                $baseValue = ($subTotal + $shippingCost) - $costTotal;

                $totalValue = substr_replace($baseValue, '.', strlen($baseValue) - 2, 0);

                if ($totalValue <= 0) {
                    $totalValue = 1;
                }

                $tokenApi = $sale->project->notazzIntegration->token_api;

                $fields = json_encode([
                                          'METHOD'                 => 'create_nfse',//Método a ser utilizado
                                          'API_KEY'                => $tokenApi,
                                          'DESTINATION_NAME'       => $sale->client->name,// Nome completo do cliente
                                          'DESTINATION_TAXID'      => $sale->client->document,//CPF ou CNPJ, somente números
                                          //'DESTINATION_IE'         => '',//Inscrição Estadual (opcional), somente números
                                          //'DESTINATION_IM'         => '',//Inscrição Municipal (opcional), somente números
                                          'DESTINATION_TAXTYPE'    => 'F',//F = Física, J = Jurídica, E = Estrangeiro
                                          'DESTINATION_STREET'     => $sale->delivery->street,//Rua do cliente
                                          'DESTINATION_NUMBER'     => (($sale->delivery->number ?? 'S/N') == 0) ? 'S/N' : $sale->delivery->number,//Número
                                          'DESTINATION_COMPLEMENT' => $sale->delivery->complement,//Complemento
                                          'DESTINATION_DISTRICT'   => $sale->delivery->neighborhood,//Bairro
                                          'DESTINATION_CITY'       => $sale->delivery->city,//Cidade, informar corretamente o nome da cidade sem abreviações
                                          'DESTINATION_UF'         => $sale->delivery->state,//Sigla do estado
                                          'DESTINATION_ZIPCODE'    => $sale->delivery->zip_code,//CEP, somente números
                                          'DESTINATION_PHONE'      => $sale->client->telephone,//Telefone do cliente (opcional), somente números
                                          'DESTINATION_EMAIL'      => $sale->client->email,//E-mail do cliente (opcional)

                                          'DESTINATION_EMAIL_SEND' => [
                                              '1' => [
                                                  'EMAIL' => $sale->client->email,
                                              ],
                                          ],//e-mail(s) que será enviado a nota depois de emitida (opcional).

                                          'DOCUMENT_BASEVALUE'   => $totalValue,//Valor total da nota fiscal. Utilizar ponto para separar as casas decimais
                                          'DOCUMENT_DESCRIPTION' => 'Prestação de Serviço em intermediação de compra, desconsiderando outros custos',//Descrição da nota fiscal (obrigatório somente para o método create_nfse e update_nfse)
                                          'DOCUMENT_COMPETENCE'  => date("Y-m-d"), //Competência (opcional), se não informado ou informado inválido será utilizado a data de hoje. Utilizar o padrão YYYY-mm-dd
                                          //'DOCUMENT_CNAE'        => '8599604', //CNAE, somente números (opcional), se não informado ou informado inválido será utilizado o padrão das configurações da empresa. Documentação: http://www.cnae.ibge.gov.br
                                          //'SERVICE_LIST_LC116'   => '0802', //Item da Lista de Serviço da Lei Complementar 116 (opcional), somente números. Caso não seja informado será utilizado o padrão da empresa. Documentação: http://www.fazenda.mg.gov.br/empresas/legislacao_tributaria/ricms/anexoxiii2002.pdf
                                          //'WITHHELD_ISS'         => '0', // ISS retido na fonte (opcional). 1 = Retido e 0 = Não retido. Se não informado ou informado inválido será utilizado o padrão das configurações da empresa
                                          //'CITY_SERVICE_CODE'    => '12345', // Código de serviço do município (opcional), somente números. Se não seja informado será utilizado o padrão da empresa
                                          /*
                                                                                'ALIQUOTAS' => [
                                                                                    'COFINS' => '0.00', // Porcentagem (%) - Utilizar ponto para separar as casas decimais
                                                                                    'CSLL'   => '0.00', // Porcentagem (%) - Utilizar ponto para separar as casas decimais
                                                                                    'INSS'   => '0.00', // Porcentagem (%) - Utilizar ponto para separar as casas decimais
                                                                                    'IR'     => '0.00', // Porcentagem (%) - Utilizar ponto para separar as casas decimais
                                                                                    'PIS'    => '0.00', // Porcentagem (%) - Utilizar ponto para separar as casas decimais
                                                                                    'ISS'    => '2.00', // Porcentagem (%) - Utilizar ponto para separar as casas decimais
                                                                                ], // Opcional - se não informado ou informado inválido será utilizado o padrão das configurações da empresa
                                          */
                                          'EXTERNAL_ID'          => $notazzInvoice->external_id, // ID externo do documento que será enviado
                                      ]);

                $notazzInvoice->update([
                                           'currency_quotation_id' => $lastUsdQuotation->id,
                                           'attempts'              => $notazzInvoice->attempts + 1,
                                           'data_json'             => $fields,
                                           'date_last_attempt'     => Carbon::now(),
                                       ]);

                $result = $this->sendRequest($fields);

                $notazzSentHistoryModel->create([
                                                    'notazz_invoice_id' => $notazzInvoice->id,
                                                    'sent_type_enum'    => $notazzSentHistoryModel->present()
                                                                                                  ->getType('sent'),
                                                    'url'               => self::NotazzUrlApi,
                                                    'data_sent'         => $fields,
                                                    'response'          => json_encode($result),
                                                ]);

                return $result;
            } else {
                return false;
            }
        } else {
            //venda nao encontrada
            return false;
        }
    }

    /**
     * @param $notazzInvoiceId
     * @return bool|mixed
     */
    public function updateNfse($notazzInvoiceId)
    {
        $notazzInvoiceModel       = new NotazzInvoice();
        $notazzSentHistoryModel   = new NotazzSentHistory();
        $saleModel                = new Sale();
        $productPlanModel         = new ProductPlan();
        $currencyQuotationService = new CurrencyQuotationService();

        $notazzInvoice = $notazzInvoiceModel->with([
                                                       'sale',
                                                       'sale.client',
                                                       'sale.delivery',
                                                       'sale.shipping',
                                                       'sale.plansSales.plan.products',
                                                       'sale.project.notazzIntegration',
                                                   ])->find($notazzInvoiceId);

        $sale = $notazzInvoice->sale;
        if ($sale) {
            //venda encontrada

            if (!empty($notazzInvoice->notazz_id)) {
                //id do notazz existe

                $sale = $saleModel->with(['plansSales'])->find($sale->id);

                $productsSale = collect();
                /** @var PlanSale $planSale */
                foreach ($sale->plansSales as $planSale) {
                    /** @var ProductPlan $productPlan */
                    foreach ($planSale->plan->productsPlans as $productPlan) {

                        $product = $productPlan->product()->first();

                        if (!empty($productPlan->cost)) {
                            //pega os valores de productplan
                            $product['product_cost']       = preg_replace("/[^0-9]/", "", $productPlan->cost);
                            $product['product_cost']       = (is_numeric($product['product_cost'])) ? $product['product_cost'] : 0;
                            $product['currency_type_enum'] = $productPlan->currency_type_enum;
                        } else {
                            //pega os valores de produto
                            if (!empty($product->cost)) {
                                $product['product_cost'] = preg_replace("/[^0-9]/", "", $product->cost);
                                $product['product_cost'] = (is_numeric($product['product_cost'])) ? $product['product_cost'] : 0;
                            } else {
                                $product['product_cost'] = 0;
                            }

                            $product['currency_type_enum'] = $product->currency_type_enum ?? 1;
                        }

                        $product['product_amount'] = $productPlan->amount ?? 1;

                        $productsSale->add($product);
                    }
                }

                $products = $productsSale;

                if ($products) {
                    $costTotal = 0;

                    foreach ($products as $product) {

                        if ($product['currency_type_enum'] == $productPlanModel->present()->getCurrency('USD')) {
                            //moeda USD
                            $lastUsdQuotation        = $currencyQuotationService->getLastUsdQuotation();
                            $product['product_cost'] = (int) ($product['product_cost'] * ($lastUsdQuotation->value / 100));
                        }

                        $costTotal += (int) ($product['product_cost'] * $product['product_amount']);
                    }

                    $shippingCost = preg_replace("/[^0-9]/", "", $sale->shipment_value);

                    $subTotal  = preg_replace("/[^0-9]/", "", $sale->sub_total);
                    $baseValue = ($subTotal + $shippingCost) - $costTotal;

                    $totalValue = substr_replace($baseValue, '.', strlen($baseValue) - 2, 0);

                    if ($totalValue <= 0) {
                        $totalValue = 1;
                    }

                    $tokenApi = $sale->project->notazzIntegration->token_api;

                    $fields = json_encode([

                                              'METHOD'                 => 'update_nfse',//Método a ser utilizado
                                              'API_KEY'                => $tokenApi,
                                              'DESTINATION_NAME'       => $sale->client->name,// Nome completo do cliente
                                              'DESTINATION_TAXID'      => $sale->client->document,//CPF ou CNPJ, somente números
                                              //'DESTINATION_IE'         => '',//Inscrição Estadual (opcional), somente números
                                              //'DESTINATION_IM'         => '',//Inscrição Municipal (opcional), somente números
                                              'DESTINATION_TAXTYPE'    => 'F',//F = Física, J = Jurídica, E = Estrangeiro
                                              'DESTINATION_STREET'     => $sale->delivery->street,//Rua do cliente
                                              'DESTINATION_NUMBER'     => $sale->delivery->number,//Número
                                              'DESTINATION_COMPLEMENT' => $sale->delivery->complement,//Complemento
                                              'DESTINATION_DISTRICT'   => $sale->delivery->neighborhood,//Bairro
                                              'DESTINATION_CITY'       => $sale->delivery->city,//Cidade, informar corretamente o nome da cidade sem abreviações
                                              'DESTINATION_UF'         => $sale->delivery->state,//Sigla do estado
                                              'DESTINATION_ZIPCODE'    => $sale->delivery->zip_code,//CEP, somente números
                                              'DESTINATION_PHONE'      => $sale->client->telephone,//Telefone do cliente (opcional), somente números
                                              'DESTINATION_EMAIL'      => $sale->client->email,//E-mail do cliente (opcional)

                                              'DESTINATION_EMAIL_SEND' => [
                                                  '1' => [
                                                      'EMAIL' => $sale->client->email,
                                                  ],
                                              ],//e-mail(s) que será enviado a nota depois de emitida (opcional).

                                              'DOCUMENT_BASEVALUE'   => $totalValue,//Valor total da nota fiscal. Utilizar ponto para separar as casas decimais
                                              'DOCUMENT_DESCRIPTION' => 'Prestação de Serviço em intermediação de compra, desconsiderando outros custos',//Descrição da nota fiscal (obrigatório somente para o método create_nfse e update_nfse)
                                              'DOCUMENT_COMPETENCE'  => date("Y-m-d"), //Competência (opcional), se não informado ou informado inválido será utilizado a data de hoje. Utilizar o padrão YYYY-mm-dd
                                              //'DOCUMENT_CNAE'        => '8599604', //CNAE, somente números (opcional), se não informado ou informado inválido será utilizado o padrão das configurações da empresa. Documentação: http://www.cnae.ibge.gov.br
                                              //'SERVICE_LIST_LC116'   => '0802', //Item da Lista de Serviço da Lei Complementar 116 (opcional), somente números. Caso não seja informado será utilizado o padrão da empresa. Documentação: http://www.fazenda.mg.gov.br/empresas/legislacao_tributaria/ricms/anexoxiii2002.pdf
                                              //'WITHHELD_ISS'         => '0', // ISS retido na fonte (opcional). 1 = Retido e 0 = Não retido. Se não informado ou informado inválido será utilizado o padrão das configurações da empresa
                                              //'CITY_SERVICE_CODE'    => '12345', // Código de serviço do município (opcional), somente números. Se não seja informado será utilizado o padrão da empresa

                                              /*'ALIQUOTAS' => [
                                                  'COFINS' => '0.00', // Porcentagem (%) - Utilizar ponto para separar as casas decimais
                                                  'CSLL'   => '0.00', // Porcentagem (%) - Utilizar ponto para separar as casas decimais
                                                  'INSS'   => '0.00', // Porcentagem (%) - Utilizar ponto para separar as casas decimais
                                                  'IR'     => '0.00', // Porcentagem (%) - Utilizar ponto para separar as casas decimais
                                                  'PIS'    => '0.00', // Porcentagem (%) - Utilizar ponto para separar as casas decimais
                                                  'ISS'    => '2.00', // Porcentagem (%) - Utilizar ponto para separar as casas decimais
                                              ], // Opcional - se não informado ou informado inválido será utilizado o padrão das configurações da empresa*/

                                              'DOCUMENT_ID' => $notazzInvoice->notazz_id,//Código retornado pelo sistema após utilizar o método create_nfse ou create_nfe_55. Utilizar esta variável para o método consult_nfe_55, consult_nfse, delete_nfe_55, delete_nfse, update_nfe_55, update_nfse
                                              'EXTERNAL_ID' => $notazzInvoice->external_id, // ID externo do documento que será atualizado
                                          ]);

                    $result = $this->sendRequest($fields);

                    $notazzSentHistoryModel->create([
                                                        'notazz_invoice_id' => $notazzInvoice->id,
                                                        'sent_type_enum'    => $notazzSentHistoryModel->present()
                                                                                                      ->getType('update'),
                                                        'url'               => self::NotazzUrlApi,
                                                        'data_sent'         => $fields,
                                                        'response'          => json_encode($result),
                                                    ]);

                    return $result;
                } else {
                    return false;
                }
            } else {
                //id do notazz nao existe
                return false;
            }
        } else {
            //venda nao encontrada
            return false;
        }
    }

    /**
     * @param $notazzInvoiceId
     * @return bool|mixed
     */
    public function consultNfse($notazzInvoiceId)
    {
        $notazzInvoiceModel     = new NotazzInvoice();
        $notazzSentHistoryModel = new NotazzSentHistory();

        $notazzInvoice = $notazzInvoiceModel->with([
                                                       'sale',
                                                       'sale.client',
                                                       'sale.delivery',
                                                       'sale.shipping',
                                                       'sale.plansSales.plan.products',
                                                       'sale.project.notazzIntegration',
                                                   ])->find($notazzInvoiceId);

        $sale = $notazzInvoice->sale;
        if ($sale) {
            //venda encontrada

            if (!empty($notazzInvoice->notazz_id)) {
                //id do notazz existe

                $tokenApi = $sale->project->notazzIntegration->token_api;

                $fields = json_encode([
                                          'METHOD'      => 'consult_nfse',//Método a ser utilizado
                                          'API_KEY'     => $tokenApi,
                                          'DOCUMENT_ID' => $notazzInvoice->notazz_id,//Código retornado pelo sistema após utilizar o método create_nfse ou create_nfe_55. Utilizar esta variável para o método consult_nfe_55, consult_nfse, delete_nfe_55, delete_nfse, update_nfe_55, update_nfse
                                          'EXTERNAL_ID' => $notazzInvoice->external_id, // ID externo do documento que será consultado
                                      ]);

                $result = $this->sendRequest($fields);

                $notazzSentHistoryModel->create([
                                                    'notazz_invoice_id' => $notazzInvoice->id,
                                                    'sent_type_enum'    => $notazzSentHistoryModel->present()
                                                                                                  ->getType('consult'),
                                                    'url'               => self::NotazzUrlApi,
                                                    'data_sent'         => $fields,
                                                    'response'          => json_encode($result),
                                                ]);

                return $result;
            } else {
                //id do notazz nao existe
                return false;
            }
        } else {
            //venda nao encontrada
            return false;
        }
    }

    /**
     * @param $notazzInvoiceId
     * @param null $startDate
     * @param null $finalDate
     * @param null $status
     * @param null $invoiceNumber
     * @return bool|mixed
     */
    public function consultAllNfse($notazzInvoiceId, $startDate = null, $finalDate = null, $status = null, $invoiceNumber = null)
    {
        $notazzInvoiceModel = new NotazzInvoice();

        $notazzInvoice = $notazzInvoiceModel->with([
                                                       'sale',
                                                       'sale.client',
                                                       'sale.delivery',
                                                       'sale.shipping',
                                                       'sale.plansSales.plan.products',
                                                       'sale.project.notazzIntegration',
                                                   ])->find($notazzInvoiceId);

        $sale = $notazzInvoice->sale;
        if ($sale) {
            //venda encontrada

            if (!empty($notazzInvoice->notazz_id)) {
                //id do notazz existe

                $tokenApi = $sale->project->notazzIntegration->token_api;

                $filters = [
                    'INITIAL_DATE' => $startDate ?? Carbon::now()
                                                          ->toDateString(), // Data Inicial. Campo obrigatório, enviar no formato yyyy-mm-dd
                    'FINAL_DATE'   => $finalDate ?? Carbon::now()
                                                          ->toDateString(), // Data Final. Campo obrigatório, enviar no formato yyyy-mm-dd
                    'STATUS'       => $status, // Status da nota fiscal. Campo opcional
                    'NUMBER'       => $invoiceNumber, // Número da nota fiscal já autorizada ou cancelada. Campo opcional
                ];

                $filters = array_filter($filters);

                $fields = json_encode([
                                          'METHOD'  => 'consult_all_nfse',//Método a ser utilizado
                                          'API_KEY' => $tokenApi,
                                          'FILTER'  => $filters,
                                      ]);

                return $this->sendRequest($fields);
            } else {
                //id do notazz nao existe
                return false;
            }
        } else {
            //venda nao encontrada
            return false;
        }
    }

    /**
     * @param $notazzInvoiceId
     * @return bool|mixed
     */
    public function cancelNfse($notazzInvoiceId)
    {
        $notazzInvoiceModel = new NotazzInvoice();

        $notazzInvoice = $notazzInvoiceModel->with([
                                                       'sale',
                                                       'sale.client',
                                                       'sale.delivery',
                                                       'sale.shipping',
                                                       'sale.plansSales.plan.products',
                                                       'sale.project.notazzIntegration',
                                                   ])->find($notazzInvoiceId);

        $sale = $notazzInvoice->sale;
        if ($sale) {
            //venda encontrada

            if (!empty($notazzInvoice->notazz_id)) {
                //id do notazz existe

                $tokenApi = $sale->project->notazzIntegration->token_api;

                $fields = json_encode([
                                          'METHOD'      => 'cancel_nfse',//Método a ser utilizado
                                          'API_KEY'     => $tokenApi,
                                          'DOCUMENT_ID' => $notazzInvoice->notazz_id,//Código retornado pelo sistema após utilizar o método create_nfse ou create_nfe_55. Utilizar esta variável para o método consult_nfe_55, consult_nfse, delete_nfe_55, delete_nfse, update_nfe_55, update_nfse
                                          'EXTERNAL_ID' => $notazzInvoice->external_id, // ID externo do documento que será cancelado
                                      ]);

                return $this->sendRequest($fields);
            } else {
                //id do notazz nao existe
                return false;
            }
        } else {
            //venda nao encontrada
            return false;
        }
    }

    /**
     * @param $notazzInvoiceId
     * @return bool|mixed
     */
    public function deleteNfse($notazzInvoiceId)
    {
        $notazzInvoiceModel = new NotazzInvoice();

        $notazzInvoice = $notazzInvoiceModel->with([
                                                       'sale',
                                                       'sale.client',
                                                       'sale.delivery',
                                                       'sale.shipping',
                                                       'sale.plansSales.plan.products',
                                                       'sale.project.notazzIntegration',
                                                   ])->find($notazzInvoiceId);

        $sale = $notazzInvoice->sale;
        if ($sale) {
            //venda encontrada

            if (!empty($notazzInvoice->notazz_id)) {
                //id do notazz existe

                $tokenApi = $sale->project->notazzIntegration->token_api;

                $fields = json_encode([
                                          'METHOD'      => 'delete_nfse',//Método a ser utilizado
                                          'API_KEY'     => $tokenApi,
                                          'DOCUMENT_ID' => $notazzInvoice->notazz_id,//Código retornado pelo sistema após utilizar o método create_nfse ou create_nfe_55. Utilizar esta variável para o método consult_nfe_55, consult_nfse, delete_nfe_55, delete_nfse, update_nfe_55, update_nfse
                                          'EXTERNAL_ID' => $notazzInvoice->external_id, // ID externo do documento que será removido
                                      ]);

                return $this->sendRequest($fields);
            } else {
                //id do notazz nao existe
                return false;
            }
        } else {
            //venda nao encontrada
            return false;
        }
    }

    /**
     * @param $tokenApi
     * @param $state
     * @param $city
     * @return mixed
     */
    public function checkCity($tokenApi, $state, $city)
    {
        $fields = json_encode([
                                  'METHOD' => 'cidades_atendidas',//Método a ser utilizado

                                  'API_KEY' => $tokenApi,//Sua chave de acesso API_KEY. Para obter a chave, acesse o menu Configurações > Empresas. Cada empresa terá sua chave de comunicação com a API
                                  'FILTER'  => [
                                      'STATE' => $state, // Sigla do estado
                                      'CITY'  => $city, // Sigla da cidade
                                  ],
                              ]);

        return $this->sendRequest($fields);
    }

    public function createNfe55($data)
    {

    }

    public function updateNfe55($data)
    {

    }

    public function consultNfe55($data)
    {

    }

    public function consultAllNfe55($data)
    {

    }

    public function cancelNfe55($data)
    {

    }

    public function deleteNfe55($data)
    {

    }

    public function updateStock($data)
    {

    }

    /**
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function verifyPendingInvoices()
    {

        $notazzInvoiceModel = new NotazzInvoice();

        $notazzInvoices = $notazzInvoiceModel->with([
                                                        'sale',
                                                        'notazzIntegration',
                                                    ])
                                             ->whereIn('status', [
                                                 $notazzInvoiceModel->present()->getStatus('pending'),
                                                 $notazzInvoiceModel->present()->getStatus('error'),
                                             ])
            //->whereColumn('attempts', '<', 'max_attempts')
                                             ->where('schedule', '<', Carbon::now())
                                             ->limit(40)
                                             ->get();

        foreach ($notazzInvoices as $notazzInvoice) {
            //cria as jobs para enviar as invoices
            SendNotazzInvoiceJob::dispatch($notazzInvoice->id)->delay(rand(1, 3));
        }
    }

    /**
     * @param $notazzIntegrationId
     * @param $saleId
     * @param int $invoiceType
     * @param null $invoiceSchedule
     * @return bool
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function createInvoice($notazzIntegrationId, $saleId, $invoiceType = 1, $invoiceSchedule = null)
    {

        if (!empty($saleId) && !empty($notazzIntegrationId)) {
            if (empty($invoiceSchedule)) {
                //executar 1h depois
                $schedule = Carbon::now()->addHour()->toDateTime();
            } else {
                $schedule = $invoiceSchedule;
            }

            $notazzInvoiceModel = new NotazzInvoice();

            $notazzInvoice = $notazzInvoiceModel->create([
                                                             'sale_id'               => $saleId,
                                                             'notazz_integration_id' => $notazzIntegrationId,
                                                             'invoice_type'          => $invoiceType,
                                                             'notazz_id'             => null,
                                                             'status'                => $notazzInvoiceModel->present()
                                                                                                           ->getStatus('pending'),
                                                             'canceled_flag'         => false,
                                                             'schedule'              => $schedule,
                                                             'date_pending'          => Carbon::now(),
                                                         ]);
            $notazzInvoice->update([
                                       'external_id' => $notazzInvoice->id,
                                   ]);

            if ($notazzInvoice) {
                return true;
            } else {
                return false;
            }
        } else {
            //nenhum venda selecionada
            return false;
        }
    }

    /**
     * @param $projectId
     * @param $startData
     * @return int
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function createOldInvoices($startData, $projectId = null)
    {
        $saleModel    = new Sale();
        $createdCount = 0;

        $sales = $saleModel->with(['project', 'project.notazzIntegration'])
                           ->has('project.notazzIntegration')
                           ->doesnthave('notazzInvoices')
                           ->whereBetween('created_at', [$startData, Carbon::now()->toDateTimeString()])
                           ->where('status', $saleModel->present()->getStatus('approved'));

        if (!empty($projectId)) {
            $sales->whereHas('project', function($queryProject) use ($projectId) {
                $queryProject->where('id', $projectId);
            });
        }

        $sales = $sales->get();

        if ($sales->isNotEmpty()) {
            //existe vendas sem invoices gerados, gerar todas
            foreach ($sales as $sale) {
                $invoiceCreated = $this->createInvoice($sale->project->notazzIntegration->id, $sale->id, $sale->project->notazzIntegration->invoice_type);
                if ($invoiceCreated) {
                    $createdCount++;
                } else {
                    Log::warnning('NotazzInvoice não criado. (notazzService - createOldInvoices) saleId=' . $sale->id);
                    report(new Exception('NotazzInvoice não criado. (notazzService - createOldInvoices) saleId=' . $sale->id));
                }
            }

            return $createdCount;
        } else {
            //todas as vendas possuem invoices já gerados
            return $createdCount;
        }
    }

    /**
     * Gerar todas as invoices
     */
    public function generateRetroactiveInvoices()
    {
        try {

            $notazzService     = new NotazzService();
            $notazzIntegration = new NotazzIntegration();
            $pusherService     = new PusherService();

            $integrations = $notazzIntegration->with([
                                                         'project',
                                                         'user',
                                                     ])
                                              ->whereNotNull('start_date')
                                              ->whereNull('retroactive_generated_date')
                                              ->get();

            foreach ($integrations as $integration) {
                $count = $notazzService->createOldInvoices($integration->start_date, $integration->project_id);
                $integration->update([
                                         'retroactive_generated_date' => Carbon::now(),
                                     ]);

                $data = [
                    'message' => 'Integração com a Notazz para o projeto ' . $integration->project->name . ' foi concluida. As Notas fiscais apartir da data ' . Carbon::parse($integration->start_date)
                                                                                                                                                                       ->format('d/m/Y') . ' foram agendadas para o envio',
                    'user'    => $integration->user->id,
                ];

                /** @var UserNotificationService $userNotificationService */
                $userNotificationService = app(UserNotificationService::class);
                if ($userNotificationService->verifyUserNotification($integration->user, $this->userNotification)) {
                    $pusherService->sendPusher($data);
                    $integration->user->notify(new RetroactiveNotazzNotification($data["message"]));
                }
            }
        } catch (Exception $e) {
            Log::warning('NotazzService - generateRetroactiveInvoices - error');
            report($e);
        }
    }

    /**
     * Gerar as notas das vendas aprovadas
     */
    public function generateInvoicesSalesApproved()
    {
        try {

            $notazzService     = new NotazzService();
            $notazzIntegration = new NotazzIntegration();

            $integrations = $notazzIntegration->with([
                                                         'project',
                                                         'user',
                                                     ])
                                              ->get();

            foreach ($integrations as $integration) {
                $count = $notazzService->createOldInvoices($integration->start_date, $integration->project_id);
            }
        } catch (Exception $e) {
            Log::warning('NotazzService - generateInvoicesSalesApproved - error');
            report($e);
        }
    }

    /**
     * @param $notazzInvoiceId
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function sendInvoice($notazzInvoiceId)
    {
        $notazzService      = new NotazzService();
        $notazzInvoiceModel = new NotazzInvoice();
        $sendGridService    = new SendgridService();
        $saleService        = new SaleService();

        $notazzInvoice = $notazzInvoiceModel->with([
                                                       'sale.user',
                                                   ])
                                            ->find($notazzInvoiceId);

        if ($notazzInvoice->attempts < $notazzInvoice->max_attempts) {
            //ainda nao chegou no maximo de tentativas

            $notazzInvoice->update([
                                       'status' => $notazzInvoiceModel->present()
                                                                      ->getStatus('in_process'),
                                   ]);

            if ($notazzInvoice->invoice_type == $notazzInvoiceModel->present()->getInvoiceType('service')) {
                //nota de servico

                $result = $notazzService->sendNfse($notazzInvoiceId);
            } else if ($notazzInvoice->invoice_type == $notazzInvoiceModel->present()->getInvoiceType('product')) {
                //nota de produto

                //$result = $notazzService->sendNfse($this->notazzInvoiceId);
            } else {
                //erro ?

                $notazzInvoice->update([
                                           'return_message'   => 'Invoice Type Error',
                                           'return_http_code' => '500',
                                           'schedule'         => $notazzInvoice->schedule,
                                           'date_error'       => Carbon::now(),
                                           'status'           => $notazzInvoiceModel->present()
                                                                                    ->getStatus('error'), //error
                                           'attempts'         => $notazzInvoice->max_attempts + 1,

                                       ]);

                Log::warning('Type da invoice invalido (SendNotazzInvoiceJob - handle)');
                report(new Exception('NotazzInvoice - type da invoice invalido, invoice : ' . $notazzInvoice->id));
            }

            if (!empty($result)) {
                if (($result->codigoProcessamento == '000')) {
                    //200 code
                    $notazzInvoice->update([
                                               'return_message'   => $result->motivo ?? null,
                                               'return_status'    => $result->statusProcessamento,
                                               'return_http_code' => $result->codigoProcessamento,
                                               'date_sent'        => Carbon::now(),
                                               'notazz_id'        => $result->id,
                                               'date_error'       => null, //clear o ultimo erro
                                               'status'           => $notazzInvoiceModel->present()
                                                                                        ->getStatus('send'), //send

                                           ]);
                } else {
                    //qualquer outro erro, remarcar invoice para ser enviado depois

                    $notazzInvoice->update([
                                               'return_message'   => $result->motivo ?? null,
                                               'return_status'    => $result->statusProcessamento,
                                               'return_http_code' => $result->codigoProcessamento,
                                               'schedule'         => Carbon::now()
                                                                           ->addHour()->toDateTime(),
                                               'date_error'       => Carbon::now(),
                                               'status'           => $notazzInvoiceModel->present()
                                                                                        ->getStatus('error'), //error
                                           ]);
                }
            } else {
                //venda invalida
                $notazzInvoice->update([
                                           'return_message'   => 'Venda não localizada',
                                           'return_http_code' => '500',
                                           'schedule'         => $notazzInvoice->schedule,
                                           'date_error'       => Carbon::now(),
                                           'status'           => $notazzInvoiceModel->present()
                                                                                    ->getStatus('error'), //error
                                           'attempts'         => $notazzInvoice->max_attempts + 1,

                                       ]);
            }
        } else {
            //chegou no maximo de tentativa, report e alertar vendedor?

            $notazzInvoice->update([
                                       'status' => $notazzInvoiceModel->present()
                                                                      ->getStatus('error_max_attempts'),
                                   ]);

            $data = [
                'name'        => $notazzInvoice->sale->user->name, //nome do produtor
                'transaction' => Hashids::connection('sale_id')->encode($notazzInvoice->sale->id), // code da venda
                'date'        => (new Carbon($notazzInvoice->sale->end_date))->format('d/m/Y H:i:s'), //data da aprovacao
            ];

            $sendGridService->sendEmail('noreply@cloudfox.net', 'Cloudfox', $notazzInvoice->sale->user->email, $notazzInvoice->sale->user->name, 'd-d65e83a8aa7e44c19b13d8b1cce0176c', $data);

            Log::warning('Maximo de tentativas para o envio da invoice (SendNotazzInvoiceJob - handle)');
            report(new Exception('NotazzInvoice - Maximo de tentativas, invoice : ' . $notazzInvoice->id));
        }
    }
}
