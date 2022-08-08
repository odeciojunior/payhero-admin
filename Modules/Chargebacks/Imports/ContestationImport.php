<?php

namespace Modules\Chargebacks\Imports;

use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Core\Entities\SaleContestation;
use Modules\Core\Entities\SaleGatewayRequest;
use Modules\Core\Services\FoxUtils;

class ContestationImport implements ToCollection
{
    protected $titles = [];
    protected $date = [];
    protected $nsu = "";
    protected $cloudfox_code = "7762088";

    public function collection(Collection $rows)
    {
        try {
            foreach ($rows as $key => $values) {
                //cabeçalho
                if ($key === 0) {
                    $date = $values[1];
                    $this->date = \Carbon\Carbon::createFromFormat("dmY", $date);
                    continue;
                }

                //nome das colunas
                if ($key === 1) {
                    $this->titles = $values;
                    continue;
                }

                //            0 => "Tipo de Registro"
                //            1 => "Número Sequencial"
                //            2 => "Número de Atividade"
                //            3 => "Cartão"
                //            4 => "Código da Loja"
                //            5 => "Motivo do Chargeback"
                //            6 => "Data da Transação"
                //            7 => "Número do Resumo de Venda"
                //            8 => "Número de Referência"
                //            9 => "Codigo do Motivo de Chargeback"
                //            10 => "Código da autorização"
                //            11 => "Data da Solicitação"
                //            12 => "Data do Retorno"
                //            13 => "Número da planilha"
                //            14 => "Valor do Chargeback"
                //            15 => "Modalidade da Venda"
                //            16 => "Reservado para Uso Futuro"
                //            17 => "Valor da Transação"
                //            18 => "NSU"
                //            19 => "Código do Terminal"
                //            20 => "Mensagem"
                //            21 => "Bandeira"
                //            22 => "Processo"
                //            23 => "Moeda"
                //            24 => "Reservado para Uso Futuro"

                $array_with_headers = $this->format($values);

                if ($array_with_headers["Código da Loja"] != $this->cloudfox_code) {
                    continue;
                }

                //OBS: O NSU TA VINDO NO CODIGO DE AUTORIZAÇÃO, ISSO NA PLANILHA DO DIA 22/02/2021 ISSO PODE MUDAR
                if (isset($array_with_headers["Código da autorização"])) {
                    $this->nsu = $array_with_headers["Código da autorização"];
                    $data_transacao = \Carbon\Carbon::createFromFormat("dmY", $values[6]);

                    $saleGatewayRequests = SaleGatewayRequest::where(
                        "gateway_result->credit->terminal_nsu",
                        "=",
                        $this->nsu
                    )
                        ->whereHas("sale", function ($query) use ($values, $data_transacao) {
                            $query
                                ->where("original_total_paid_value", "=", FoxUtils::onlyNumbers($values[17]))
                                ->whereDate("start_date", $data_transacao->format("Y-m-d"));
                        })
                        ->get();

                    //                    if (!$saleGatewayRequests->count()) {
                    //
                    //
                    //                        //faz a busca no banco de produção pra salvaar local, "OBS: essa tabela não é migrada"
                    //                        try {
                    //                            $saleGatewayRequest = new SaleGatewayRequest();
                    //                            $sale_gateway_requests_productions = $saleGatewayRequest->setConnection('mysql_production')
                    //                                ->where('gateway_result->credit->terminal_nsu', '=', $this->nsu)
                    //                                ->whereHas('sale', function ($query) use ($values, $data_transacao) {
                    //                                    $query->where('original_total_paid_value', '=', FoxUtils::onlyNumbers($values[17]))
                    //                                        ->whereDate('start_date', $data_transacao->format('Y-m-d'));
                    //                                })->get();
                    //
                    //                        } catch (\Exception $e) {
                    //                            dd($e->getMessage());
                    //                        }
                    //
                    //                        if ($sale_gateway_requests_productions->count()) {
                    //
                    //                            //salva no banco local os sale_gateway_requests
                    //                            foreach ($sale_gateway_requests_productions as $sale_gateway_requests_production) {
                    //
                    //                                $sale_const = new SaleGatewayRequest();
                    //                                $sale_const->sale_id = $sale_gateway_requests_production->sale_id;
                    //                                $sale_const->gateway_id = $sale_gateway_requests_production->gateway_id;
                    //                                $sale_const->send_data = $sale_gateway_requests_production->send_data;
                    //                                $sale_const->gateway_result = $sale_gateway_requests_production->gateway_result;
                    //                                $sale_const->gateway_exceptions = $sale_gateway_requests_production->gateway_exceptions;
                    //                                $sale_const->save();
                    //
                    //                            }
                    //
                    //                            //faz a busca novamente pra trazer nos ids correto
                    //                            $saleGatewayRequests = SaleGatewayRequest::where('gateway_result->credit->terminal_nsu', '=', $this->nsu)
                    //                                ->whereHas('sale', function ($query) use ($values, $data_transacao) {
                    //                                    $query->where('original_total_paid_value', '=', FoxUtils::onlyNumbers($values[17]))
                    //                                        ->whereDate('start_date', $data_transacao->format('Y-m-d'));
                    //                                })->get();
                    //
                    //                        }
                    //
                    //                    }

                    if ($saleGatewayRequests->count() == 0) {
                        report(new Exception("Nsu " . $this->nsu . " não encontrado "));
                        continue;
                    }

                    if ($saleGatewayRequests->count() > 1) {
                        report(new Exception("Nsu " . $this->nsu . " com mais de uma venda" . $this->date));
                        continue;
                    }

                    $sale_id = $saleGatewayRequests->first()->sale->id ?? null;
                    $sale_contestation = SaleContestation::where("sale_id", $sale_id)->first();

                    if ($sale_id && empty($sale_contestation)) {
                        $sale_contestation = new SaleContestation();
                        $sale_contestation->sale_id = $saleGatewayRequests->first()->sale->id ?? null;
                        $sale_contestation->data = json_encode($array_with_headers);
                        $sale_contestation->nsu = $this->nsu;
                        $sale_contestation->file_date = $this->date;
                        $sale_contestation->transaction_date = isset($array_with_headers["Data da Transação"])
                            ? \Carbon\Carbon::createFromFormat("dmY", $array_with_headers["Data da Transação"])
                            : null;
                        $sale_contestation->expiration_date = isset($array_with_headers["Data do Retorno"])
                            ? \Carbon\Carbon::createFromFormat("dmY", $array_with_headers["Data do Retorno"])
                            : null;
                        $sale_contestation->request_date = isset($array_with_headers["Data da Solicitação"])
                            ? \Carbon\Carbon::createFromFormat("dmY", $array_with_headers["Data da Solicitação"])
                            : null;
                        $sale_contestation->reason = isset($array_with_headers["Motivo do Chargeback"])
                            ? $array_with_headers["Motivo do Chargeback"]
                            : null;
                        $sale_contestation->save();
                    } elseif (!empty($sale_contestation)) {
                        $sale_contestation->data = json_encode($array_with_headers);
                        $sale_contestation->nsu = $this->nsu;
                        $sale_contestation->file_date = $this->date;
                        $sale_contestation->transaction_date = isset($array_with_headers["Data da Transação"])
                            ? \Carbon\Carbon::createFromFormat("dmY", $array_with_headers["Data da Transação"])
                            : null;
                        $sale_contestation->expiration_date = isset($array_with_headers["Data do Retorno"])
                            ? \Carbon\Carbon::createFromFormat("dmY", $array_with_headers["Data do Retorno"])
                            : null;
                        $sale_contestation->request_date = isset($array_with_headers["Data da Solicitação"])
                            ? \Carbon\Carbon::createFromFormat("dmY", $array_with_headers["Data da Solicitação"])
                            : null;
                        $sale_contestation->reason = isset($array_with_headers["Motivo do Chargeback"])
                            ? $array_with_headers["Motivo do Chargeback"]
                            : null;
                        $sale_contestation->save();
                    }
                }
            }
        } catch (\Exception $e) {
            report(new Exception("Falha na importação do arquivo  " . $this->nsu));
            report($e);
        }
    }

    private function format($values)
    {
        $arr = [];
        foreach ($values as $key => $value) {
            $arr[$this->titles[$key]] = $value;
        }
        return $arr;
    }
}
