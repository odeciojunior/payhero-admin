<?php 

namespace Modules\Core\Helpers;

use App\User;
use App\Plano;
use App\Venda;
use App\Comprador;
use App\ZenviaSms;
use Carbon\Carbon;
use App\PlanoVenda;
use App\MensagemSms;
use Modules\Core\Sms\ServicoSmsHelper;

class AgendamentosSms {


    public static function verificarBoletosVencendo(){

        $boletos = Venda::whereDate('vencimento_boleto', '=', Carbon::today()->toDateString())
                        ->where('pagamento_status','waiting_payment')
                        ->get()->toArray();

        foreach($boletos as $boleto){

            $user = User::find($boleto['proprietario']);

            if($user == null){
                continue;
            }

            if($user['sms_zenvia_qtd'] > 0){

                $planoVenda = PlanoVenda::where('venda',$boleto['id'])->first();

                $servico_sms = ZenviaSms::where([
                    ['plano', $planoVenda['plano']],
                    ['evento', 'boleto_vencendo'],
                    ['status', '1']
                ])->first();

                if($servico_sms){
                    if(ServicoSmsHelper::enviarSms($servico_sms,Venda::find($boleto['id']))){
                        $user->update([
                            'sms_zenvia_qtd' => $user['sms_zenvia_qtd'] - 1
                        ]);
                    }
                }
            }
        }

    }

    public static function verificarBoletosVencidos(){

        $boletos = Venda::whereDate('vencimento_boleto', '<', Carbon::today()->toDateString())
                        ->where('pagamento_status','waiting_payment')
                        ->get()->toArray();

        foreach($boletos as $boleto){

            $user = User::find($boleto['proprietario']);

            if($user == null){
                continue;
            }

            if($user['sms_zenvia_qtd'] > 0){

                $planoVenda = PlanoVenda::where('venda',$boleto['id'])->first();
                $comprador = Comprador::find($boleto['comprador']);

                $mensagem_enviada = MensagemSms::where([
                    ['plano', $planoVenda['plano']],
                    ['evento', 'boleto_vencido'],
                    ['para',"55".preg_replace("/[^0-9]/", '', $comprador['telefone'])]
                ])->first();

                if($mensagem_enviada){
                    continue;
                }

                $servico_sms = ZenviaSms::where([
                    ['plano', $planoVenda['plano']],
                    ['evento', 'boleto_vencido'],
                    ['status', '1']
                ])->first();

                if($servico_sms){
                    if(ServicoSmsHelper::enviarSms($servico_sms,Venda::find($boleto['id']))){
                        $user->update([
                            'sms_zenvia_qtd' => $user['sms_zenvia_qtd'] - 1
                        ]);
                    }
                }
            }
        }

    }

}
