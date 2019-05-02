<?php 

namespace Modules\Core\Tranferencias;

use App\User;
use App\Plano;
use App\Venda;
use App\Comprador;
use App\Transacao;
use Carbon\Carbon;
use App\PlanoVenda;
use App\MensagemSms;
use Modules\Core\Sms\ServicoSmsHelper;

class Transferencias {

    public static function verify(){

        $transacoes = Transacao::where('data_liberacao',Carbon::now()->format('Y-m-d'))->get()->toArray();

        foreach($transacoes as $transacao){


        }

    }

}
