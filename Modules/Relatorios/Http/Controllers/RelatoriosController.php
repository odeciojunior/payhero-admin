<?php

namespace Modules\Relatorios\Http\Controllers;

use App\Foto;
use App\Plano;
use App\Venda;
use App\Entrega;
use App\Projeto;
use App\Comprador;
use Carbon\Carbon;
use PagarMe\Client;
use App\PlanoVenda; 
use App\UserProjeto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Controllers\Controller;
use Modules\Relatorios\DataTables\VendasDataTable;
use Modules\Relatorios\Transformers\VendasResource;

class RelatoriosController extends Controller {

    public function index(VendasDatatable $dataTable){

        return $dataTable->render('relatorios::vendas');
    }

    public function vendas() {

        // \Ebanx\Config::set([
        //     'integrationKey' => 'test_ik_WkYi7P55VR0bk_ZcxtgGTA',
        //     'testMode'       => true,
        // ]);

        // $result = \Ebanx\Ebanx::doExchange([
        //     'currency_code' => 'USD'
        // ]);

        // $response = \Ebanx\Ebanx::doQuery(array(
        //     'hash' => '5cc75099ebcf4ba417fbebc282fd49830ad3d1f34ad53681'
        // ));

        // dd($response);

        $projetosUsuario = UserProjeto::where('user', \Auth::user()->id)->get()->toArray();
        $projetos = [];

        foreach($projetosUsuario as $projetoUsuario){
            $projeto = Projeto::find($projetoUsuario['projeto']);
            $projetos[] = [
                'id' => $projeto['id'],
                'nome' => $projeto['nome']
            ];
        }

        return view('relatorios::vendas',[
            'projetos' => $projetos,
        ]);
    }

    public function dadosVendas(){

        $vendas = \DB::table('vendas as venda')
            ->leftjoin('compradores as comprador', 'comprador.id', '=', 'venda.comprador')
            ->get([
                'venda.id',
                'comprador.nome',
                'venda.meio_pagamento',
                'venda.forma_pagamento',
                'venda.pagamento_status',
                'venda.data_inicio',
                'venda.data_finalizada',
                'venda.valor_plano',
        ]);

        return Datatables::of($vendas)
        ->addColumn('plano_nome', function ($venda) {
            $planos_venda = PlanoVendad::where('venda',$venda->id)->get()->toArray();
            if(count($planos_venda) > 1){
                return "Carrinho";
            }
            foreach($planos_venda as $planoVenda){
                $plano = Plano::find($planoVenda);
                return $plano['nome'];
            }
        })
        ->editColumn('data_inicio', function ($venda) {
            return $venda->data_inicio ? with(new Carbon($venda->data_inicio))->format('d/m/Y H:i:s') : '';
        })
        ->editColumn('data_finalizada', function ($venda) {
            return $venda->data_finalizada ? with(new Carbon($venda->data_finalizada))->format('d/m/Y H:i:s') : '';
        })
        ->editColumn('forma_pagamento', function ($venda) {
            return $venda->forma_pagamento == 'cartao_credito' ? 'cartão de crédito' : $venda->forma_pagamento;
        })
        ->editColumn('pagamento_status', function ($venda) {
            if($venda->pagamento_status == 'paid')
                return 'Aprovada';
            if($venda->pagamento_status == 'rejected')
                return 'Rejeitada';
            if($venda->pagamento_status == 'in_process')
                return 'Em processo'; 
            if($venda->pagamento_status == 'pending')
                return 'Pendente';
            return $venda->pagamento_status;
        })
        ->addColumn('detalhes', function ($venda) {
            $buttons = "<button class='btn btn-sm btn-outline btn-primary detalhes_venda' venda='".$venda->id."' data-target='#modal_detalhes' data-toggle='modal' type='button'>
                           Detalhes
                        </button>";
            if($venda->pagamento_status == 'paid'){
                $buttons .= "<button class='btn btn-sm btn-outline btn-primary detalhes_venda' venda='".$venda->id."' data-target='#modal_detalhes' data-toggle='modal' type='button'>
                                Estornar
                             </button>";
            }

            return $buttons;
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function getDetalhesVenda(Request $request){

        $dados = $request->all();
        $venda = Venda::find(preg_replace("/[^0-9]/", "", $dados['id_venda']));

        $planosVenda = PlanoVenda::where('venda', $venda->id)->get()->toArray();
        $planos = [];

        foreach($planosVenda as $key => $planoVenda){
            $planos[$key]['nome'] = Plano::find($planoVenda['plano'])['nome'];
            $planos[$key]['quantidade'] = $planoVenda['quantidade'];
        }

        $comprador = Comprador::find($venda->comprador);
        $entrega = Entrega::find($venda->entrega);

        $venda['data_inicio'] = (new Carbon($venda['data_inicio']))->format('d/m/Y H:i:s');

        $detalhes = view('relatorios::detalhes',[
            'venda'     => $venda,
            'planos'    => $planos,
            'comprador' => $comprador,
            'entrega'   => $entrega
        ]);

        return response()->json($detalhes->render());

    }

    public function estornarVenda(Request $request){

        $dados = $request->all();

        $venda = Venda::find($dados['id_venda']);

        if($venda['pagamento_id'] != ''){

            if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
                $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
            }
            else{
                $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
            }

            $transactionRefunds = $pagarMe->transactions()->refund([
                'id' => $venda['pagamento_id']
            ]);

        }
        else{
            return response()->json('ID da venda não encontrado');
        }

        return response()->json('sucesso');
    }

    public function getVendas(Request $request){

        $vendas = Venda::where('proprietario',\Auth::user()->id);

        if($request->projeto != ''){
            $planos = Plano::where('projeto',$request->projeto)->pluck('id');
            $planos_venda = PlanoVenda::whereIn('plano',$planos)->pluck('venda');
            $vendas->whereIn('id',$planos_venda);
        }

        if($request->comprador != ''){
            $compradores = Comprador::where('nome','LIKE','%'.$request->comprador.'%')->pluck('id');
            $vendas->whereIn('comprador',$compradores);
        }

        if($request->forma != ''){
            $vendas->where('forma_pagamento',$request->forma);
        }
        
        if($request->status != ''){
            $vendas->where('pagamento_status',$request->status);
        }

        if($request->data_inicial != '' && $request->data_final != ''){
            $vendas->whereBetween('data_inicio', [$request->data_inicial,date('Y-m-d', strtotime($request->data_final.' + 1 day'))]);
        }
        else{
            if($request->data_inicial != ''){
                $vendas->whereDate('data_inicio', '>', $request->data_inicial);
            }

            if($request->data_final != ''){
                $vendas->whereDate('data_inicio', '<', date('Y-m-d', strtotime($request->data_final.' + 1 day')));
            }
        }

        $vendas->orderBy('id','DESC');

        return VendasResource::collection($vendas->paginate(10));
    }

    public function detalhesVenda($id_venda){

        $venda = Venda::find(Hashids::decode($id_venda));

        if(!$venda){
            return response()->json('Venda não encontrada');
        }

        if($venda['proprietario'] != \Auth::user()->id){
            return response()->json('Não autorizado');
        }

        $planos_venda = PlanoVenda::where('venda', $venda['id'])->get()->toArray();
        $comprador = Comprador::find($venda['comprador']);
        $entrega = Entrega::find($venda['entrega']);

        $status = '';
        if($venda['pagamento_status'] == 'paid')
            $status = "Aprovada";
        elseif($venda['pagamento_status'] == 'refused')
            $status = "Rejeitada";
        elseif($venda['pagamento_status'] == 'waiting_payment')
            $status = "Aguardando pagamento";
        else
            $status = $venda['pagamento_status'];

        $produtos = [];
        foreach($planos_venda as $planoVenda){
            $plano = Plano::find($planoVenda['plano']);
            $produtos[] = [
                'nome' => $plano['nome'],
                'quantidade' => $planoVenda['quantidade']
            ];
        }

        $dados = [];

        $dados['codigo_transacao'] = "#".$venda['id'];
        $dados['forma_pagamento'] = $venda['forma_pagamento'];
        $dados['data'] = (new Carbon($venda['data_inicio']))->format('d/m/Y H:i:s');
        $dados['status'] = $status;
        $dados['codigo_transacao'] = "#".$venda['id'];
        $dados['produtos'] = $produtos;
        $dados['comprador_nome'] = $comprador['nome'];
        $dados['comprador_email'] = $comprador['email'];
        $dados['comprador_cpf'] = $comprador['cpf_cnpj'];
        $dados['comprador_telefone'] = $comprador['telefone'];
        $dados['comprador_nome'] = $comprador['nome'];
        if($entrega){
            $dados['valor_frete'] = $entrega['valor_frete'];
            $dados['rua'] = $entrega['rua'];
            $dados['numero_casa'] = $entrega['numero'];
            $dados['rua'] = $entrega['rua'];
            $dados['complemento'] = $entrega['ponto_referencia'];
            $dados['bairro'] = $entrega['bairro'];
            $dados['cidade'] = $entrega['cidade'];
            $dados['estado'] = $entrega['estado'];
            $dados['cep'] = $entrega['cep'];
        }
        if($venda['forma_pagamento'] == 'Boleto'){
            $dados['link_boleto'] = $venda['link_boleto'];
            $dados['linha_digitavel_boleto'] = $venda['linha_digitavel_boleto'];
        }

        return response()->json($dados);

    }


}
