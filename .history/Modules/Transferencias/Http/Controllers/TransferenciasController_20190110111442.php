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

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $saldo_disponivel = 0;
        $saldo_transferido = 0;
        $saldo_futuro = 0;
        $saldo_antecipavel = 0;

        $empresas_usuario = UsuarioEmpresa::where('user',\Auth::user()->id)->orderBy('id')->get()->toArray();

        $empresa_selecionada = false;
        $empresas = [];

        foreach($empresas_usuario as $empresa_usuario){
            $empresa = Empresa::find($empresa_usuario['empresa']);

            $empresas[] = [
                'id' => $empresa['id'],
                'nome' => $empresa['nome_fantasia']
            ];

            if($empresa['recipient_id'] == ''){
                continue;
            }

            if(!$empresa_selecionada){
                $empresa_ativa = $empresa['id'];
                $empresa_selecionada = true;

                $anticipationLimits = $pagarMe->bulkAnticipations()->getList([
                    // 'requested_amount' => $dados['valor'],
                    // 'build' => 'true',
                    'recipient_id' => $empresa['recipient_id'],
                    // 'payment_date' => strtotime(Carbon::now()->addDays(7)->format('Y-m-d')) * 1000,
                    // 'timeframe' => 'start',
                ]);

//                 foreach($anticipationLimits as $anticipationLimit){
//                     echo $anticipationLimit->id.'<br>';
//                     $canceledAnticipation = $pagarMe->bulkAnticipations()->delete([
//                         'recipient_id' => $empresa['recipient_id'],
//                         'bulk_anticipation_id' => $anticipationLimit->id,
//                     ]);
//                 }
// die;
                $recipientBalance = $pagarMe->recipients()->getBalance([
                    'recipient_id' => $empresa['recipient_id'],
                ]);
    
                $saldo_disponivel  += $recipientBalance->available->amount;
                $saldo_transferido += $recipientBalance->transferred->amount;
                $saldo_futuro      += $recipientBalance->waiting_funds->amount;

                $anticipationLimits = $pagarMe->bulkAnticipations()->getLimits([
                    'recipient_id' => $empresa['recipient_id'],
                    'payment_date' => strtotime(Carbon::now()->addDays(7)->format('Y-m-d')) * 1000,
                    'timeframe' => 'start'
                ]);

                $saldo_antecipavel = $anticipationLimits->maximum->amount;
            }

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
        $saldo_antecipavel = substr_replace($saldo_antecipavel, '.',strlen($saldo_antecipavel) - 2, 0 );
        $saldo_antecipavel = number_format($saldo_antecipavel,2);

        return view('transferencias::index',[
            'saldo_disponivel'  => $saldo_disponivel,
            'saldo_transferido' => $saldo_transferido,
            'saldo_futuro'      => $saldo_futuro,
            'saldo_antecipavel' => $saldo_antecipavel,
            'empresa'           => $empresa_ativa,
            'empresas'          => $empresas
        ]);

    }

    public function detalhesAntecipacao(Request $request){

        $dados = $request->all();

        $empresa = Empresa::find($dados['empresa']);

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $anticipationLimits = $pagarMe->bulkAnticipations()->create([
            'requested_amount' => $dados['valor'],
            'build' => 'true',
            'recipient_id' => $empresa['recipient_id'],
            'payment_date' => strtotime(Carbon::now()->addDays(7)->format('Y-m-d')) * 1000,
            'timeframe' => 'start',
        ]);

        $canceledAnticipation = $pagarMe->bulkAnticipations()->delete([
            'recipient_id' => $empresa['recipient_id'],
            'bulk_anticipation_id' => $anticipationLimits->id,
        ]);

        $dados = [];

        $dados['taxa'] = substr_replace($dados['taxa'], '.',strlen($dados['taxa']) - 2, 0 );
        $dados['taxa'] = number_format($dados['taxa'],2);

        $dados['taxa_de_antecipacao'] = $anticipationLimits->anticipation_fee;
        $dados['taxa_de_antecipacao'] = number_format($dados['taxa_de_antecipacao'],2);
        
        $dados['valor_total'] = $anticipationLimits->amount;
        $dados['valor_total'] = number_format($dados['valor_total'],2);

        $dados['data_liberacao'] = $anticipationLimits->payment_date;

        return response()->json($dados);
    }

}


