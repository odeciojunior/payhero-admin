<?php

namespace Modules\Finances\Http\Controllers;

use Carbon\Carbon;
use PagarMe\Client;
use App\Entities\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class FinancesController extends Controller {

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $userCompanies = Company::where('user', \Auth::user()->id)->get()->toArray();

        $selectedCompany = false;
        $companies = [];

        foreach($userCompanies as $company){

            $companies[] = [
                'id'   => $company['id'],
                'name' => $company['fantasy_name']
            ];

            if(!$selectedCompany){
                $company_ativa = $company['id'];
                $selectedCompany = true;
            }

        }

        return view('finances::index',[
            'company'   => $selectedCompany,
            'companies' => $companies
        ]);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saque(Request $request){

        $dados = $request->all();

        $company = Company::find($dados['company']);

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }
    
        $transfer = $pagarMe->transfers()->create([
            'amount' => $dados['valor'],
            'recipient_id' => $company['recipient_id']
        ]);

        return response()->json('sucesso');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransferencias(Request $request){

        $dados = $request->all();

        $company = Company::find($dados['company']);

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $recipientTransfers = $pagarMe->transfers()->getList([
            'recipient_id' => $company['recipient_id']
        ]);

        $historico_transferencias = [];

        foreach($recipientTransfers as $recipientTransfer){

            $historico = []; 
            $historico['valor'] = $recipientTransfer->amount;
            $historico['valor'] = substr_replace($historico['valor'], '.',strlen($historico['valor']) - 2, 0 );
            $historico['valor'] = number_format($historico['valor'],2);

            $historico['data_solicitacao'] = $recipientTransfer->date_created;
            $historico['data_solicitacao'] = Carbon::parse($historico['data_solicitacao'])->format('d/m/Y');

            $historico['data_liberacao'] = $recipientTransfer->funding_estimated_date;
            $historico['data_liberacao'] = Carbon::parse($historico['data_liberacao'])->format('d/m/Y');

            $historico['status'] = $recipientTransfer->status;
            if($historico['status'] == 'pending_transfer'){
                $historico['status'] = 'Transferência pendente';
            }
            elseif($historico['status'] == 'canceled'){
                $historico['status'] = 'Cancelada';
            }

            $historico['id'] = $recipientTransfer->id;

            $historico_transferencias[] = $historico;
        }

        return response()->json($historico_transferencias);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelarTransferencia(Request $request){

        $dados = $request->all();

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $canceledTransfer = $pagarMe->transfers()->cancel([
            'id' => $dados['id_transferencia']
        ]);

        return response()->json('sucesso');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelarAntecipacao(Request $request){

        $dados = $request->all();

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $company = Company::find($dados['company']);

        $canceledAnticipation = $pagarMe->bulkAnticipations()->cancel([
            'recipient_id' => $company['recipient_id'],
            'bulk_anticipation_id' => $dados['id_antecipacao']
        ]);

        return response()->json('sucesso');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detalhesAntecipacao(Request $request){

        $dados = $request->all();

        $company = Company::find($dados['company']);

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $antecipacao = false;
        $qtd_dias = 1;

        while(!$antecipacao){
            try{
                $anticipationLimits = $pagarMe->bulkAnticipations()->create([
                    'requested_amount' => $dados['valor'],
                    'build' => 'true',
                    'recipient_id' => $company['recipient_id'],
                    'payment_date' => strtotime(Carbon::now()->addDays($qtd_dias)->format('Y-m-d')) * 1000,
                    'timeframe' => $dados['data_simulacao'],
                ]);
                $antecipacao = true;
            }
            catch(\Exception $e){
                $qtd_dias++;
            }
        }

        $canceledAnticipation = $pagarMe->bulkAnticipations()->delete([
            'recipient_id' => $company['recipient_id'],
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmarAntecipacao(Request $request){

        $dados = $request->all();

        $company = Company::find($dados['company']);

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $antecipacao = false;
        $qtd_dias = 1;

        while(!$antecipacao){
            try{
                $anticipationLimits = $pagarMe->bulkAnticipations()->create([
                    'requested_amount' => $dados['valor'],
                    'build' => 'true',
                    'recipient_id' => $company['recipient_id'],
                    'payment_date' => strtotime(Carbon::now()->addDays($qtd_dias)->format('Y-m-d')) * 1000,
                    'timeframe' => $dados['data_simulacao'],
                ]);
                $antecipacao = true;
            }
            catch(\Exception $e){
                $qtd_dias++;
            }
        }

        $confirmedAnticipation = $pagarMe->bulkAnticipations()->confirm([
            'recipient_id' => $company['recipient_id'],
            'bulk_anticipation_id' => $anticipationLimits->id,
        ]);

        return response()->json('sucesso');

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAntecipacoes(Request $request){

        $dados = $request->all();

        $company = Company::find($dados['company']);

        if(!$company['recipient_id']){
            return response()->json("Dados bancários não encontrados");
        }

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $antecipacoes = $pagarMe->bulkAnticipations()->getList([
            'recipient_id' => $company['recipient_id']
        ]);

        $historico_antecipacoes = [];

        foreach($antecipacoes as $antecipacao){

            $historico = []; 
            $historico['valor'] = $antecipacao->amount;
            $historico['valor'] = substr_replace($historico['valor'], '.',strlen($historico['valor']) - 2, 0 );
            $historico['valor'] = number_format($historico['valor'],2);

            $historico['data_solicitacao'] = $antecipacao->date_created;
            $historico['data_solicitacao'] = Carbon::parse($historico['data_solicitacao'])->format('d/m/Y');

            $historico['data_liberacao'] = $antecipacao->payment_date;
            $historico['data_liberacao'] = Carbon::parse($historico['data_liberacao'])->format('d/m/Y');

            $historico['status'] = $antecipacao->status;
            if($historico['status'] == 'building'){
                $historico['status'] = 'Transferência pendente';
            }
            elseif($historico['status'] == 'pending'){
                $historico['status'] = 'Transferência pendente';
            }
            elseif($historico['status'] == 'canceled'){
                $historico['status'] = 'Cancelada';
            }
    
            $historico['id'] = $antecipacao->id;    

            $historico_antecipacoes[] = $historico;
        }

        return response()->json($historico_antecipacoes);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function extrato(Request $request){

        $userCompanies = Company::where('user', \Auth::user()->id)->get()->toArray();

        $selectedCompany = false;
        $preSelectedCompany;
        $companies = [];

        foreach($userCompanies as $company){

            $companies[] = [
                'id' => $company['id'],
                'nome' => $company['fantasy_name']
            ];

            if(!$selectedCompany){
                $preSelectedCompany = $company;
                $selectedCompany = true;
            }

        }

        $startDateFilter = Carbon::now()->format('Y-m-d');
        $endDateFilter   = Carbon::now()->addMonths(1)->format('Y-m-d');

        return view('finances::extrato',[
            'company'            => $preSelectedCompany,
            'companies'          => $companies,
            'filtro_data_inicio' => $startDateFilter,
            'filtro_data_fim'    => $endDateFilter
        ]);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detalhesSaldoFuturo(Request $request){

        $dados = $request->all();

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $company = Company::find($dados['company']);

        $transactionPayables = $pagarMe->payables()->getList([
            'recipient_id' => $company['recipient_id'],
            'count' => 200,
            'payment_date' =>  '>='.strtotime($dados['filtro_inicio']) * 1000,
            'payment_date' =>  '<='.strtotime($dados['filtro_fim']) * 1000
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
                else if($transactionPayable->payment_method == 'boleto'){
                    $dados_transacao['metodo'] = 'Boleto';
                }
                else{
                    $dados_transacao['metodo'] = $transactionPayable->payment_method;
                }

                if($transactionPayable->status == 'waiting_funds' || $transactionPayable->status == 'prepaid'){
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
        }

        array_multisort($lancamentos_futuros,$array_data);

        foreach($lancamentos_futuros as &$lancamentos_futuro){
            $lancamentos_futuro['data_pagamento'] = date('d/m/Y',strtotime($lancamentos_futuro['data_pagamento']));
        }
        
        return response()->json($lancamentos_futuros);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function historico(Request $request){

        $dados = $request->all();

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $company = Company::find($dados['company']);

        if(!$company['recipient_id']){
            return response()->json('Conta bancária não configurada!');
        }

        $transactionPayables = $pagarMe->payables()->getList([
            'recipient_id' => $company['recipient_id'],
            'count' => 200,
        ]);

        $historicos = [];

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
                elseif($transactionPayable->payment_method == 'boleto'){
                    $dados_transacao['metodo'] = 'Boleto';
                }
                else{
                    $dados_transacao['metodo'] = $transactionPayable->payment_method;
                }

                $historicos[] = $dados_transacao;
            }
        }

        $array_data = [];
        foreach($historicos as &$historico){
            $array_data[] = $historico['data_pagamento'];
            $historico['data_pagamento'] = date('d/m/Y',strtotime($historico['data_pagamento']));
        }

        array_multisort($historicos,$array_data);

        return response()->json($historicos);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

        $company = Company::find($dados['company']);

        if($company['recipient_id'] == ''){
            return response()->json("Configurações da conta bancaria não encontradas");
        }

        $recipientBalance = $pagarMe->recipients()->getBalance([
            'recipient_id' => $company['recipient_id'],
        ]);

        $saldo_disponivel  += $recipientBalance->available->amount;
        $saldo_transferido += $recipientBalance->transferred->amount;
        $saldo_futuro      += $recipientBalance->waiting_funds->amount;

        $antecipacao = false;
        $qtd_dias = 1;
        while(!$antecipacao){
            try{
                $anticipationLimits = $pagarMe->bulkAnticipations()->getLimits([
                    'recipient_id' => $company['recipient_id'],
                    'payment_date' => strtotime(Carbon::now()->addDays($qtd_dias)->format('Y-m-d')) * 1000,
                    'timeframe' => 'start'
                ]);

                $antecipacao = true;
            }
            catch(\Exception $e){
                $qtd_dias++;
            }
        }

        $saldo_antecipavel = $anticipationLimits->maximum->amount;

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

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSaldosDashboard(){

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

        $userCompanies = Company::where('user',\Auth::user()->id)->get()->toArray();

        foreach($userCompanies as $company){
            if($company['recipient_id'] == ''){
                continue;
            }

            $recipientBalance = $pagarMe->recipients()->getBalance([
                'recipient_id' => $company['recipient_id'],
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

        return response()->json([
            'saldo_disponivel'  => $saldo_disponivel,
            'saldo_transferido' => $saldo_transferido,
            'saldo_futuro'      => $saldo_futuro,
        ]);
        
    }

}


