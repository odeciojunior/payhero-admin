<?php

namespace Modules\Core\Tranferencias;

use App\User;
use App\Empresa;
use App\Transacao;
use Carbon\Carbon;
use App\Transferencia;
use Modules\Core\Sms\ServicoSmsHelper;

class Transferencias {

    public static function verify(){

        $transacoes = Transacao::where('data_liberacao',Carbon::now()->format('Y-m-d'))->get()->toArray();

        foreach($transacoes as $t){

            $empresa = Empresa::find($t['empresa']);

            $user = User::find($empresa['user']);

            Transferencia::create([
                'transacao' => $t['id'],
                'user'      => $empresa['user'],
                'valor'     => $t['valor'],
                'tipo'      => 'entrada',
            ]);

            $transacao = Transacao::find($t['id']);

            $transacao->update([
                'status' => 'transferido'
            ]);

            $user->update([
                'saldo' => $user['saldo'] + substr_replace($t['valor'], '.',strlen($t['valor']) - 2, 0 ) 
            ]);

        }

    }

}
