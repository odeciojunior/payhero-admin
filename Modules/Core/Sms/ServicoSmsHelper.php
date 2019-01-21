<?php

namespace Modules\Core\Sms;

use App\Plano;
use App\Venda;
use App\Dominio;
use DateTimeZone;
use App\Comprador;
use App\ZenviaSms;
use App\PlanoVenda;
use App\MensagemSms;
use Zenvia\Model\Sms;
use Zenvia\Model\SmsFacade;

class ServicoSmsHelper{

    public static function enviarSms(ZenviaSms $servico_sms, Venda $venda){

        $comprador = Comprador::find($venda->comprador);
        $plano_venda = PlanoVenda::where('venda',$venda->id)->first();
        $plano = Plano::find($plano_venda->plano);

        $nomes = explode(" ",$comprador['nome']);
        $dominio = Dominio::where('projeto',$plano['projeto'])->first();
        $url_checkout = "https://checkout.".$dominio->dominio."/".$plano['cod_identificador'];

        $mensagem = str_replace("{primeiro_nome}", $nomes[0],$servico_sms->mensagem);
        $mensagem = str_replace("{nome_completo}", $comprador['nome'],$mensagem);
        $mensagem = str_replace("{email}", $comprador['email'],$mensagem);
        $mensagem = str_replace("{url_checkout}", $url_checkout,$mensagem);
        $mensagem = str_replace("{url_boleto}", $venda['link_boleto'],$mensagem);
        $mensagem = str_replace("{data_vencimento}", $venda['vencimento_boleto'],$mensagem);
        $mensagem = str_replace("{linha_digitavel}", $venda['linha_digitavel_boleto'],$mensagem);

        $smsFacade = new SmsFacade('healthlab.corp','hLQNVb7VQk');
        $sms = new Sms();
        $sms->setTo('55'.preg_replace("/[^0-9]/", "", $comprador['telefone']));
        $sms->setMsg($mensagem);
        $id_sms = uniqid();
        $sms->setId($id_sms);
        $sms->setCallbackOption(Sms::CALLBACK_NONE);
        $date = new \DateTime();
        $date->setTimeZone(new DateTimeZone('America/Sao_Paulo'));
        if($servico_sms->tempo != '0'){
            $date->modify("+{$servico_sms->tempo} {$servico_sms->periodo}");
        }
        $schedule = $date->format("Y-m-d\TH:i:s");
        $sms->setSchedule($schedule);

        try{
            $response = $smsFacade->send($sms);

            MensagemSms::create([
                'id_zenvia' => $id_sms,
                'para' => '55'.preg_replace("/[^0-9]/", "", $comprador['telefone']),
                'mensagem' => $mensagem,
                'data' => $schedule,
                'status' => $response->getStatusDescription(),
                'plano' => $plano['id'],
                'evento' => $servico_sms->evento,
                'tipo' => 'Enviada'
            ]);

            return true;
        }
        catch(\Exception $ex){

            MensagemSms::create([
                'id_zenvia' => $id_sms,
                'para' => '55'.preg_replace("/[^0-9]/", "", $comprador['telefone']),
                'mensagem' => $mensagem,
                'data' => $schedule,
                'status' => 'Erro',
                'plano' => $plano_venda->plano,
                'evento' => $servico_sms->evento,
                'tipo' => 'Enviada'
            ]);

            return false;
        }

    }

}