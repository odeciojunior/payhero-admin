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

        $dados['taxa'] = $anticipationLimits->fee;
        $dados['taxa'] = substr_replace($dados['taxa'], '.',strlen($dados['taxa']) - 2, 0 );
        $dados['taxa'] = number_format($dados['taxa'],2);

        $dados['taxa_antecipacao'] = $anticipationLimits->anticipation_fee;
        $dados['taxa_antecipacao'] = substr_replace($dados['taxa_antecipacao'], '.',strlen($dados['taxa_antecipacao']) - 2, 0 );
        $dados['taxa_antecipacao'] = number_format($dados['taxa_antecipacao'],2);

        $dados['valor_total'] = $anticipationLimits->amount;
        $dados['valor_total'] = substr_replace($dados['valor_total'], '.',strlen($dados['valor_total']) - 2, 0 );
        $dados['valor_total'] = number_format($dados['valor_total'],2);

        $dados['data_liberacao'] = date('d/m/Y',strtotime($anticipationLimits->payment_date));

        return response()->json($dados);
    }


    public function extrato(Request $request){

        $dados = $request->all();

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

                $recipientBalance = $pagarMe->recipients()->getBalance([
                    'recipient_id' => $empresa['recipient_id'],
                ]);

                $transactionPayables = $pagarMe->payables()->getList([
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

        return view('transferencias::extrato',[
            'saldo_disponivel'   => $saldo_disponivel,
            'saldo_transferido'  => $saldo_transferido,
            'saldo_futuro'       => $saldo_futuro,
            'saldo_antecipavel'  => $saldo_antecipavel,
            'empresa'            => $empresa_ativa,
            'empresas'           => $empresas,
            'pagamentos_futuros' => $transactionPayables,
        ]);

    }
    
    public function detalhesSaldoFuturo(Request $request){

        $dados = $request->all();

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $empresa = Empresa::find($dados['empresa']);

        $transactionPayables = $pagarMe->payables()->getList([
            'recipient_id' => $empresa['recipient_id'],
        ]);

        $lancamentos_futuros = [];

        $hoje = date('Y-m-d');

        foreach($transactionPayables as &$transactionPayable){

            $data_pagamento = date('Y-m-d',strtotime($transactionPayable->payment_date));

            if(strtotime($data_pagamento) >= strtotime($hoje)){

                $dados_transacao = [];
                $dados_transacao['data_pagamento'] = $transactionPayable->payment_date;

                $dados_transacao['valor'] = $transactionPayable->amount;
                $dados_transacao['valor'] = substr_replace($dados_transacao['valor'], '.',strlen($dados_transacao['valor']) - 2, 0 );
                $dados_transacao['valor'] = number_format($dados_transacao['valor'],2);

                if($transactionPayable->payment_method == 'credit_card'){
                    $dados_transacao['metodo'] = 'Cartão de crédito';
                }
                else{
                    $dados_transacao['metodo'] = $transactionPayable->amount;
                }

                if($transactionPayable->status == 'waiting_funds'){
                    $dados_transacao['status'] = 'Aguardando pagamento';
                }
                else{
                    $dados_transacao['status'] = $transactionPayable->status;
                }
                $lancamentos_futuros[] = $dados_transacao;
            }
        }

        $array_data = [];
        foreach($lancamentos_futuros as &$lancamentos_futuro){
            $array_data[] = $lancamentos_futuro['data_pagamento'];
            $lancamentos_futuro['data_pagamento'] = date('d/m/Y',strtotime($lancamentos_futuro['data_pagamento']));
        }

        array_multisort($lancamentos_futuros,$array_data);

        return response()->json($lancamentos_futuros);

    }

    public function historico(Request $request){

        $dados = $request->all();

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $empresa = Empresa::find($dados['empresa']);

        $transactionPayables = $pagarMe->payables()->getList([
            'recipient_id' => $empresa['recipient_id'],
        ]);

        $lancamentos_futuros = [];

        $hoje = date('Y-m-d');

        foreach($transactionPayables as &$transactionPayable){

            $data_pagamento = date('Y-m-d',strtotime($transactionPayable->payment_date));

            if(strtotime($data_pagamento) < strtotime($hoje)){

                $dados_transacao = [];
                $dados_transacao['data_pagamento'] = $transactionPayable->payment_date;

                if($transactionPayable->status == 'waiting_funds'){
                    $dados_transacao['status'] = 'Aguardando pagamento';
                }
                elseif($transactionPayable->status == 'paid'){
                    $dados_transacao['status'] = 'Pago';
                }
                else{
                    $dados_transacao['status'] = $transactionPayable->status;
                }

                if($transactionPayable->type != 'refund'){
                    $dados_transacao['valor'] = $transactionPayable->amount;
                    $dados_transacao['valor'] = substr_replace($dados_transacao['valor'], '.',strlen($dados_transacao['valor']) - 2, 0 );
                    $dados_transacao['valor'] = number_format($dados_transacao['valor'],2);
                }
                else{
                    $dados_transacao['valor'] = str_replace('-','',$transactionPayable->amount);
                    $dados_transacao['valor'] = substr_replace($dados_transacao['valor'], '.',strlen($dados_transacao['valor']) - 2, 0 );
                    $dados_transacao['valor'] = number_format($dados_transacao['valor'],2);
                    $dados_transacao['valor'] = '-'.$dados_transacao['valor'];
                    $dados_transacao['status'] = "Pagamento estornado";
                }
                if($transactionPayable->payment_method == 'credit_card'){
                    $dados_transacao['metodo'] = 'Cartão de crédito';
                }
                if($transactionPayable->payment_method == 'boleto'){
                    $dados_transacao['metodo'] = 'Boleto';
                }
                else{
                    $dados_transacao['metodo'] = $transactionPayable->payment_method;
                }

                $lancamentos_futuros[] = $dados_transacao;
            }
        }

        $array_data = [];
        foreach($lancamentos_futuros as &$lancamentos_futuro){
            $array_data[] = $lancamentos_futuro['data_pagamento'];
            $lancamentos_futuro['data_pagamento'] = date('d/m/Y',strtotime($lancamentos_futuro['data_pagamento']));
        }

        array_multisort($lancamentos_futuros,$array_data);

        return response()->json($lancamentos_futuros);

    }

    public function getSaldos(Request $request){

        $dados = $request->all();

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

        $empresa = Empresa::find($dados['empresa']);

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

        return response()->json([
            'saldo_disponivel'  => $saldo_disponivel,
            'saldo_transferido' => $saldo_transferido,
            'saldo_futuro'      => $saldo_futuro,
            'saldo_antecipavel' => $saldo_antecipavel,
        ]);


    }


//                 foreach($anticipationLimits as $anticipationLimit){
//                     echo $anticipationLimit->id.'<br>';
//                     $canceledAnticipation = $pagarMe->bulkAnticipations()->delete([
//                         'recipient_id' => $empresa['recipient_id'],
//                         'bulk_anticipation_id' => $anticipationLimit->id,
//                     ]);
//                 }
// die;

}


