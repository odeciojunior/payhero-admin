<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Empresa;
use PagarMe\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */ 
    public function index() {

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $saldo_disponivel = 0;
        $saldo_transferido = 0;
        $saldo_futuro = 0;

        $empresas = Empresa::where('user',\Auth::user()->id)->get()->toArray();

        foreach($empresas as $empresa){

            if(!$empresa['recipient_id']){
                continue;
            }

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

        return view('dashboard::dashboard',[
            'saldo_disponivel' => $saldo_disponivel,
            'saldo_transferido' => $saldo_transferido,
            'saldo_futuro' => $saldo_futuro
        ]);


    }

}
