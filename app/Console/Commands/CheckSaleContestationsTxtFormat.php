<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Modules\Core\Entities\SaleContestation;
use Modules\Core\Entities\SaleGatewayRequest;
use Modules\Core\Services\Antifraud\CloudfoxAntifraudService;
use Modules\Core\Services\Email\Gmail\GmailService;

class CheckSaleContestationsTxtFormat extends Command
{
    protected $signature = 'getnet:import-sale-contestations-txt-format';

    protected $description = 'Import sale contestation from gmail';

    protected $cloudfoxCode = "7762088";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $gmailService = new GmailService();
            $attachmentPaths = $gmailService->getFilesFromEmail(200, false);

            foreach ($attachmentPaths as $file) {
                $replace = str_replace("storage", "", $file);
                $aArray = file(storage_path("app") . $replace, FILE_IGNORE_NEW_LINES);
                $arr_getnet = [];
                $file_date = '';

                foreach ($aArray as $key => $sLine) {
                    $codes = $this->updateCodeByLine($sLine);

                    if ($key == 0) {
                        $file_date = $this->returnByStartLine($codes, 3);
                        continue;
                    }

                    $arr = [
                        "nsu" => $this->returnByStartLine($codes, 135),
                        "card" => $this->returnByStartLine($codes, 38),
                        "store_code" => $this->returnByStartLine($codes, 57),
                        "sequential_number" => $this->returnByStartLine($codes, 3),
                        "terminal_code" => $this->returnByStartLine($codes, 251),
                        "file_date" => $file_date,
                        "return_date" => $this->returnByStartLine($codes, 149),
                        "transaction_date" => $this->returnByStartLine($codes, 77),
                        "request_date" => $this->returnByStartLine($codes, 141),
                        "modality_sale" => $this->returnByStartLine($codes, 174),
                        "spreadsheet_number" => $this->returnByStartLine($codes, 157),
                        "transaction_amount" => $this->returnByStartLine($codes, 224),
                        "reference_number" => $this->returnByStartLine($codes, 97),
                        "authorization_code" => $this->returnByStartLine($codes, 214),
                        "reason_dispute" => $this->returnByStartLine($codes, 120),
                        "transaction_code" => $this->returnByStartLine($codes, 241),
                        "protocol_number" => $this->returnByStartLine($codes, 8),
                    ];

                    if (!empty($arr['store_code']) && $arr['store_code'] == $this->cloudfoxCode) {
                        $arr_getnet[] = $arr;
                    }
                }

                foreach ($arr_getnet as $contestation_arr) {
                    $transaction_date = Carbon::createFromFormat("dmY", $contestation_arr['transaction_date']);
                    $saleGatewayRequests = SaleGatewayRequest::where("gateway_result->credit->terminal_nsu", "=",
                        $contestation_arr["nsu"])
                        ->whereHas("sale", function ($query) use ($contestation_arr, $transaction_date) {
                            $query->where("original_total_paid_value", "=",
                                foxutils()->onlyNumbers($contestation_arr['transaction_amount']))
                                ->whereDate("start_date", $transaction_date->format("Y-m-d"));
                        })->get();

                    if ($saleGatewayRequests->count() == 0) {
                        $message = 'Nsu ' . $contestation_arr["nsu"] . ' não encontrado ';
                        report(new Exception($message));
                        $this->error($message);
                        continue;
                    }

                    if ($saleGatewayRequests->count() > 1) {
                        $message = 'Nsu ' . $contestation_arr["nsu"] . ' com mais de uma venda';
                        report(new Exception($message));
                        $this->error($message);
                        continue;
                    }

                    $sale_id = $saleGatewayRequests->first()->sale->id ?? null;
                    $sale_contestation = SaleContestation::where('sale_id', $sale_id)->first();

                    if (empty($sale_contestation)) {
                        $sale_contestation = new SaleContestation();
                    }

                    $sale_contestation->sale_id = $sale_id;
                    $sale_contestation->data = json_encode($contestation_arr);
                    $sale_contestation->nsu = $contestation_arr["nsu"];
                    $sale_contestation->file_date = Carbon::createFromFormat("dmY",
                        $contestation_arr['file_date']);
                    $sale_contestation->transaction_date = isset($contestation_arr['transaction_date']) ?
                        Carbon::createFromFormat("dmY", $contestation_arr['transaction_date']) : null;
                    $sale_contestation->expiration_date = isset($contestation_arr['return_date']) ?
                        Carbon::createFromFormat("dmY", $contestation_arr['return_date']) : null;
                    $sale_contestation->request_date = isset($contestation_arr['request_date']) ?
                        Carbon::createFromFormat("dmY", $contestation_arr['request_date']) : null;
                    $sale_contestation->reason = isset($contestation_arr['reason_dispute']) ? $contestation_arr['reason_dispute'] : null;
                    $sale_contestation->save();

                    try {
                        if (in_array(
                            $sale_contestation->reason,
                            ['4837', '4863', '81', '83', '74', '103', '104', '4540', '4755', '4840', '57']
                        )) {
                            $sale = $saleGatewayRequests->first()->sale;
                            (new CloudfoxAntifraudService())->updateConfirmedFraudData($sale);
                        }
                    } catch (Exception $e) {
                        report($e);
                    }
                }
            }
        } catch (Exception $e) {
            report($e);
        }

    }

    private function updateCodeByLine($line)
    {
        $collect = new Collection();
        $codes = $this->codes();

        foreach ($codes as $code) {
            $value = substr($line, intval($code['start']) - 1);
            $value = trim(substr($value, 0, intval($code['length'])));
            $code['value'] = $value;

            $collect->push($code);
        }

        return $collect;
    }

    private function codes()
    {
        return new Collection([
            [
                "id" => "1",
                "description" => "1",
                "start" => "2",
                "end" => "2",
                "length" => "2",
                "type" => "A",
                "comments" => "Fixo \"01\" Header"
            ],
            [
                "id" => "2",
                "description" => "Data de Criação do A rquivo",
                "start" => "3",
                "end" => "10",
                "length" => "8",
                "type" => "N",
                "comments" => "Formato: DDMMA A A A"
            ],
            [
                "id" => "3",
                "description" => "Versão do layout",
                "start" => "11",
                "end" => "13",
                "length" => "3",
                "type" => "A",
                "comments" => "Fixo “V 01”"
            ],
            [
                "id" => "4",
                "description" => "Identif icação do layout",
                "start" => "14",
                "end" => "30",
                "length" => "17",
                "type" => "A",
                "comments" => "Fixo “GETNETLA C REQUEST”"
            ],
            [
                "id" => "5",
                "description" => "Espaços",
                "start" => "31",
                "end" => "260",
                "length" => "230",
                "type" => "A",
                "comments" => "Reservado para uso f uturo (brancos)"
            ],
            [
                "id" => "4",
                "description" => "Tipo de Registro",
                "start" => "1",
                "end" => "2",
                "length" => "2",
                "type" => "A",
                "comments" => "Fixo \"02\" Detalhes"
            ],
            [
                "id" => "5",
                "description" => "Numero Sequencial",
                "start" => "3",
                "end" => "7",
                "length" => "5",
                "type" => "N",
                "comments" => "Sequência das solicitações de documentos"
            ],
            [
                "id" => "6",
                "description" => "Numero do Protocolo",
                "start" => "8",
                "end" => "37",
                "length" => "30",
                "type" => "N",
                "comments" => "Número do protocolo GetNet *** UPDA TED de 8 para 30"
            ],
            [
                "id" => "7",
                "description" => "Numero do Cartão",
                "start" => "38",
                "end" => "56",
                "length" => "19",
                "type" => "A",
                "comments" => "Formato: 999999XXXXXX9999"
            ],
            [
                "id" => "9",
                "description" => "Numero da Maquineta",
                "start" => "67",
                "end" => "76",
                "length" => "10",
                "type" => "A",
                "comments" => "Reservado para uso f uturo"
            ],
            [
                "id" => "10",
                "description" => "Data da Transação",
                "start" => "77",
                "end" => "84",
                "length" => "8",
                "type" => "N",
                "comments" => "Formato: DDMMA A A A"
            ],
            [
                "id" => "11",
                "description" => "Numero do Resumo de V enda",
                "start" => "85",
                "end" => "96",
                "length" => "12",
                "type" => "A",
                "comments" => "RV  *** UPDA TED de 7 para 12"
            ],
            [
                "id" => "12",
                "description" => "Numero de Ref erencia",
                "start" => "97",
                "end" => "119",
                "length" => "23",
                "type" => "A",
                "comments" => "Número de Ref erência"
            ],
            [
                "id" => "13",
                "description" => "Motivo da Solicitação",
                "start" => "120",
                "end" => "134",
                "length" => "15",
                "type" => "A",
                "comments" => "Motivo da disputa/contestação"
            ],
            [
                "id" => "14",
                "description" => "Código da A utorização",
                "start" => "135",
                "end" => "140",
                "length" => "6",
                "type" => "A",
                "comments" => "Código da autorização"
            ],
            [
                "id" => "15",
                "description" => "Data da Solicitação",
                "start" => "141",
                "end" => "148",
                "length" => "8",
                "type" => "N",
                "comments" => "Formato: DDMMA A A A"
            ],
            [
                "id" => "16",
                "description" => "Data de Retorno",
                "start" => "149",
                "end" => "156",
                "length" => "8",
                "type" => "N",
                "comments" => "Formato: DDMMA A A A"
            ],
            [
                "id" => "17",
                "description" => "Numero da Planilha",
                "start" => "157",
                "end" => "159",
                "length" => "3",
                "type" => "N",
                "comments" => "Nº  Sequencial do lote (controle GetNet)"
            ],
            [
                "id" => "18",
                "description" => "Tipo de Solicitação RED",
                "start" => "160",
                "end" => "173",
                "length" => "14",
                "type" => "A",
                "comments" => "Reservado para uso f uturo"
            ],
            [
                "id" => "19",
                "description" => "Modalidade da V enda",
                "start" => "174",
                "end" => "213",
                "length" => "40",
                "type" => "A",
                "comments" => "Reservado para uso f uturo"
            ],
            [
                "id" => "20",
                "description" => "Código do Termo de A utorização",
                "start" => "214",
                "end" => "223",
                "length" => "10",
                "type" => "A",
                "comments" => "Zeros"
            ],
            [
                "id" => "21",
                "description" => "Valor da Transação",
                "start" => "224",
                "end" => "240",
                "length" => "17",
                "type" => "A",
                "comments" => "Composto com inteiro (N14) + separador (A 1) + decimal (N2)"
            ],
            [
                "id" => "22",
                "description" => "NSU",
                "start" => "241",
                "end" => "250",
                "length" => "10",
                "type" => "A",
                "comments" => "NSU"
            ],
            [
                "id" => "23",
                "description" => "Código do Terminal",
                "start" => "251",
                "end" => "260",
                "length" => "10",
                "type" => "A",
                "comments" => "Código do Terminal onde f oi realizada a transação"
            ],
            [
                "id" => "24",
                "description" => "Tipo de Registro",
                "start" => "1",
                "end" => "2",
                "length" => "2",
                "type" => "A",
                "comments" => "Fixo \"99\" - Trailer"
            ],
            [
                "id" => "25",
                "description" => "Data de Criação do A rquivo",
                "start" => "3",
                "end" => "10",
                "length" => "8",
                "type" => "N",
                "comments" => "Formato: DDMMA A A A"
            ],
            [
                "id" => "26",
                "description" => "Quantidade de Registros",
                "start" => "11",
                "end" => "16",
                "length" => "6",
                "type" => "N",
                "comments" => "Quantidade de Registros no arquivo"
            ],
            [
                "id" => "27",
                "description" => "Espaços",
                "start" => "17",
                "end" => "260",
                "length" => "244",
                "type" => "A",
                "comments" => "Reservado para uso f uturo (brancos)"
            ],
            [
                "id" => "Tipo de Registro",
                "description" => "1",
                "start" => "2",
                "end" => "2",
                "length" => "A",
                "type" => "Fixo \"01\" Header",
                "comments" => "2"
            ],
            [
                "id" => "Data de Criação do A rquivo",
                "description" => "3",
                "start" => "10",
                "end" => "8",
                "length" => "N",
                "type" => "Formato: DDMMA A A A",
                "comments" => "3"
            ],
            [
                "id" => "Versão do layout",
                "description" => "11",
                "start" => "13",
                "end" => "3",
                "length" => "A",
                "type" => "Fixo “V 01”",
                "comments" => "4"
            ],
            [
                "id" => "Identif icação do layout",
                "description" => "14",
                "start" => "30",
                "end" => "17",
                "length" => "A",
                "type" => "Fixo “GETNETLA C REQUEST”",
                "comments" => "5"
            ],
            [
                "id" => "Espaços",
                "description" => "31",
                "start" => "372",
                "end" => "342",
                "length" => "A",
                "type" => "Reservado para uso f uturo (brancos)",
                "comments" => "4"
            ],
            [
                "id" => "Tipo de Registro",
                "description" => "1",
                "start" => "2",
                "end" => "2",
                "length" => "A",
                "type" => "Fixo \"02\" Detalhes",
                "comments" => "MENSAGEM*"
            ],
            [
                "id" => "5",
                "description" => "Numero Sequencial",
                "start" => "3",
                "end" => "7",
                "length" => "5",
                "type" => "N",
                "comments" => "Sequência das solicitações de documentos"
            ],
            [
                "id" => "6",
                "description" => "Numero do Protocolo",
                "start" => "8",
                "end" => "37",
                "length" => "30",
                "type" => "N",
                "comments" => "Número do protocolo GetNet *** UPDA TED de 8 para 30"
            ],
            [
                "id" => "7",
                "description" => "Numero do Cartão",
                "start" => "38",
                "end" => "56",
                "length" => "19",
                "type" => "A",
                "comments" => "Formato: 999999XXXXXX9999"
            ],
            [
                "id" => "8",
                "description" => "Código da Loja",
                "start" => "57",
                "end" => "66",
                "length" => "10",
                "type" => "A",
                "comments" => "Nº  estabelecimento cadastro GetNet"
            ],
            [
                "id" => "9",
                "description" => "Numero da Maquineta",
                "start" => "67",
                "end" => "76",
                "length" => "10",
                "type" => "A",
                "comments" => "Reservado para uso f uturo"
            ],
            [
                "id" => "10",
                "description" => "Data da Transação",
                "start" => "77",
                "end" => "84",
                "length" => "8",
                "type" => "N",
                "comments" => "Formato: DDMMA A A A"
            ],
            [
                "id" => "receberá um arquivo como ilustrado abaixo.",
                "description" => "11",
                "start" => "Numero do Resumo de V enda",
                "end" => "85",
                "length" => "96",
                "type" => "12",
                "comments" => "A"
            ],
            [
                "id" => "RV  *** UPDA TED de 7 para 12",
                "description" => "12",
                "start" => "Numero de Ref erencia",
                "end" => "97",
                "length" => "119",
                "type" => "23",
                "comments" => "A"
            ],
            [
                "id" => "Número de Ref erência",
                "description" => "13",
                "start" => "Motivo da Solicitação",
                "end" => "120",
                "length" => "134",
                "type" => "15",
                "comments" => "A"
            ],
            [
                "id" => "Motivo da disputa/contestação",
                "description" => "14",
                "start" => "Código da A utorização",
                "end" => "135",
                "length" => "140",
                "type" => "6",
                "comments" => "A"
            ],
            [
                "id" => "Código da autorização",
                "description" => "15",
                "start" => "Data da Solicitação",
                "end" => "141",
                "length" => "148",
                "type" => "8",
                "comments" => "N"
            ],
            [
                "id" => "Formato: DDMMA A A A",
                "description" => "16",
                "start" => "Data de Retorno",
                "end" => "149",
                "length" => "156",
                "type" => "8",
                "comments" => "N"
            ],
            [
                "id" => "Formato: DDMMA A A A",
                "description" => "17",
                "start" => "Numero da Planilha",
                "end" => "157",
                "length" => "159",
                "type" => "3",
                "comments" => "N"
            ],
            [
                "id" => "Nº  Sequencial do lote (controle GetNet)",
                "description" => "18",
                "start" => "Tipo de Solicitação RED",
                "end" => "160",
                "length" => "173",
                "type" => "14",
                "comments" => "A"
            ],
            [
                "id" => "Reservado para uso futuro",
                "description" => "19",
                "start" => "Modalidade da V enda",
                "end" => "174",
                "length" => "213",
                "type" => "40",
                "comments" => "A"
            ],
            [
                "id" => "Reservado para uso f uturo",
                "description" => "20",
                "start" => "Código do Termo de A utorização",
                "end" => "214",
                "length" => "223",
                "type" => "10",
                "comments" => "A"
            ],
            [
                "id" => "Zeros",
                "description" => "21",
                "start" => "Valor da Transação",
                "end" => "224",
                "length" => "240",
                "type" => "17",
                "comments" => "A"
            ],
            [
                "id" => "Composto com inteiro (N14) + separador (A 1) + decimal (N2)",
                "description" => "Ao lado",
                "start" => " você encontrará informações sobre a",
                "end" => "22",
                "length" => "NSU",
                "type" => "241",
                "comments" => "250"
            ],
            [
                "id" => "10",
                "description" => "A",
                "start" => "NSU",
                "end" => "23",
                "length" => "Código do Terminal",
                "type" => "251",
                "comments" => "260"
            ],
            [
                "id" => "10",
                "description" => "A",
                "start" => "Código do Terminal onde f oi realizada a transação",
                "end" => "24",
                "length" => "Mensagem",
                "type" => "261",
                "comments" => "360"
            ],
            [
                "id" => "100",
                "description" => "A",
                "start" => "Campo texto do banco emissor com detalhe da disputa (Ex: nome portador)",
                "end" => "01-MC Crédito; 01-Mastercard; 02-Maestro; 03-Visa; 04-Visa Electron; 05-",
                "length" => "25",
                "type" => "Produto",
                "comments" => "361"
            ],
            [
                "id" => "362",
                "description" => "2",
                "start" => "N",
                "end" => "A mex; 11-Elo Crédito; 12-Elo Débito; 13-Hiper-Hipercard.",
                "length" => "26",
                "type" => "Processo (Tipo de carta)",
                "comments" => "363"
            ],
            [
                "id" => "363",
                "description" => "1",
                "start" => "A",
                "end" => "Chargegack / Request.",
                "length" => "27",
                "type" => "Reinteração",
                "comments" => "364"
            ],
            [
                "id" => "364",
                "description" => "1",
                "start" => "A",
                "end" => "Reservado para uso f uturo",
                "length" => "*Escolha um entre os dois layouts de arquivos.txt (EDI) disponíveis de acordo com a",
                "type" => "28",
                "comments" => "Data Disputa"
            ],
            [
                "id" => "365",
                "description" => "372",
                "start" => "8",
                "end" => "N",
                "length" => "Data que iniciou a disputa",
                "type" => "necessidade da sua Empresa.",
                "comments" => "#"
            ],
            [
                "id" => "29",
                "description" => "Tipo de Registro",
                "start" => "1",
                "end" => "2",
                "length" => "2",
                "type" => "A",
                "comments" => "Fixo \"99\" - Trailer"
            ],
            [
                "id" => "30",
                "description" => "Data de Criação do A rquivo",
                "start" => "3",
                "end" => "10",
                "length" => "8",
                "type" => "N",
                "comments" => "Formato: DDMMA A A A"
            ],
            [
                "id" => "31",
                "description" => "Quantidade de Registros",
                "start" => "11",
                "end" => "16",
                "length" => "6",
                "type" => "N",
                "comments" => "Quantidade de Registros no arquivo"
            ],
            [
                "id" => "32",
                "description" => "Espaços",
                "start" => "17",
                "end" => "372",
                "length" => "356",
                "type" => "A",
                "comments" => "Reservado para uso f uturo (brancos)"
            ],
        ]);
    }

    private function returnByStartLine($codes, $line_number)
    {
        $arr = $codes->where('start', $line_number)->first();
        return $arr['value'];
    }
}
