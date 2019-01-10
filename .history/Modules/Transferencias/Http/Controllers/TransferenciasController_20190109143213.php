<?php

namespace Modules\Transferencias\Http\Controllers;

use App\Empresa;
use Carbon\Carbon;
use PagarMe\Client;
use App\UsuarioEmpresa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class TransferenciasController extends Controller {


    public function index() {

        // $data = Carbon::now()->addMonths(6)->format('Y-m-d');

        // echo strtotime(Carbon::now()->addMonths(6)->format('Y-m-d')) * 1000;

        // die;


        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $saldo_disponivel = 0;
        $saldo_transferido = 0;
        $saldo_futuro = 0;

        $empresas_usuario = UsuarioEmpresa::where('user',\Auth::user()->id)->get()->toArray();

        foreach($empresas_usuario as $empresa_usuario){
            $empresa = Empresa::find($empresa_usuario['empresa']);

            $anticipationLimits = $pagarMe->bulkAnticipations()->getLimits([
                'recipient_id' => $empresa['recipient_id'],
                'payment_date' => strtotime(Carbon::now()->addMonths(6)->format('Y-m-d')) * 1000,
                'timeframe' => 'start'
            ]);
dd($anticipationLimits);
            $recipientBalance = $pagarMe->recipients()->getBalance([
                'recipient_id' => $empresa['recipient_id'],
            ]);

            $saldo_disponivel  += $recipientBalance->available->amount;
            $saldo_transferido += $recipientBalance->transferred->amount;
            $saldo_futuro      += $recipientBalance->waiting_funds->amount;
        }

        if($saldo_disponivel == 0){
            $saldo_disponivel = '000';
        }
        if($saldo_transferido == 0){
            $saldo_transferido = '000';
        }
        if($saldo_futuro == 0){
            $saldo_futuro = '000';
        }

        $saldo_disponivel = substr_replace($saldo_disponivel, '.',strlen($saldo_disponivel) - 2, 0 );
        $saldo_disponivel = number_format($saldo_disponivel,2);
        $saldo_transferido = substr_replace($saldo_transferido, '.',strlen($saldo_transferido) - 2, 0 );
        $saldo_transferido = number_format($saldo_transferido,2);
        $saldo_futuro = substr_replace($saldo_futuro, '.',strlen($saldo_futuro) - 2, 0 );
        $saldo_futuro = number_format($saldo_futuro,2);

        return view('transferencias::index',[
            'saldo_disponivel' => $saldo_disponivel,
            'saldo_transferido' => $saldo_transferido,
            'saldo_futuro' => $saldo_futuro
        ]);
        
    }

}
