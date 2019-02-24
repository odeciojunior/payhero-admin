<?php

namespace Modules\Relatorios\Http\Controllers;

use App\Foto;
use App\Plano;
use App\Venda;
use App\Entrega;
use App\Comprador;
use Carbon\Carbon;
use App\PlanoVenda;
use PagarMe\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;
use Illuminate\Routing\Controller; 
use Modules\Relatorios\DataTables\VendasDataTable;

class RelatoriosController extends Controller {

    public function index(VendasDatatable $dataTable){

        return $dataTable->render('relatorios::vendas');
    }
 
    public function vendas(VendasDataTable $dataTable) {

        return $dataTable->render('relatorios::vendas');
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
            foreach($planos_venda as $plano_venda){
                $plano = Plano::find($plano_venda);
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
        $venda = Venda::find($dados['id_venda']);
        $planos_venda = PlanoVenda::where('venda', $venda->id)->get()->toArray();
        $comprador = Comprador::find($venda->comprador);
        $entrega = Entrega::find($venda->entrega);

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td colspan='2' class='text-center'><b>INFORMAÇÕES GERAIS</b></td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Código da transação:</b></td>";
        $modal_body .= "<td>#".$venda['id']."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Forma de pagamento:</b></td>";
        $modal_body .= "<td>".$venda['forma_pagamento']."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Data:</b></td>";
        $modal_body .= "<td>".(new Carbon($venda['data_inicio']))->format('d/m/Y H:i:s')."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Status:</b></td>";
        if($venda['pagamento_status'] == 'paid')
            $modal_body .= "<td>Aprovada</td>";
        elseif($venda['pagamento_status'] == 'refused')
            $modal_body .= "<td>Rejeitada</td>";
        elseif($venda['pagamento_status'] == 'waiting_payment')
            $modal_body .= "<td>Aguardando pagamento</td>";
        else
            $modal_body .= "<td>".$venda['pagamento_status']."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td colspan='2' class='text-center'><b>PRODUTOS DA VENDA</b></td>";
        $modal_body .= "</tr>";
        foreach($planos_venda as $plano_venda){
            $plano = Plano::find($plano_venda['plano']);
            $modal_body .= "<tr>";
            $modal_body .= "<td><b>Produto:</b></td>";
            $modal_body .= "<td>".$plano['nome']."</td>";
            $modal_body .= "</tr>";
            $modal_body .= "<tr>";
            $modal_body .= "<td><b>Quantidade:</b></td>";
            $modal_body .= "<td>".$plano_venda['quantidade']."</td>";
            $modal_body .= "</tr>";
        }
        $modal_body .= "<tr>";
        $modal_body .= "<td colspan='2' class='text-center'><b>INFORMAÇÕES DO CLIENTE</b></td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Comprador:</b></td>";
        $modal_body .= "<td>".$comprador['nome']."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>CPF:</b></td>";
        $modal_body .= "<td>".$comprador['cpf_cnpj']."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Email:</b></td>";
        $modal_body .= "<td>".$comprador['email']."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Telefone:</b></td>";
        $modal_body .= "<td>".$comprador['telefone']."</td>";
        $modal_body .= "</tr>";
        if($entrega){
            $modal_body .= "<tr>";
            $modal_body .= "<td colspan='2' class='text-center'><b>INFORMAÇÕES DA ENTREGA</b></td>";
            $modal_body .= "</tr>";
            $modal_body .= "<tr>";
            $modal_body .= "<td><b>Valor do frete:</b></td>";
            $modal_body .= "<td>".$venda['valor_frete']."</td>";
            $modal_body .= "</tr>";
            $modal_body .= "<tr>";
            $modal_body .= "<td><b>Rua:</b></td>";
            $modal_body .= "<td>".$entrega['rua']."</td>";
            $modal_body .= "</tr>";
            $modal_body .= "<tr>";
            $modal_body .= "<td><b>Número:</b></td>";
            $modal_body .= "<td>".$entrega['numero']."</td>";
            $modal_body .= "</tr>";
            $modal_body .= "<tr>";
            $modal_body .= "<td><b>Complemento:</b></td>";
            $modal_body .= "<td>".$entrega['ponto_referencia']."</td>";
            $modal_body .= "</tr>";
            $modal_body .= "<tr>";
            $modal_body .= "<td><b>Bairro:</b></td>";
            $modal_body .= "<td>".$entrega['bairro']."</td>";
            $modal_body .= "</tr>";
            $modal_body .= "<tr>";
            $modal_body .= "<td><b>Cidade:</b></td>";
            $modal_body .= "<td>".$entrega['cidade']."</td>";
            $modal_body .= "</tr>";
            $modal_body .= "<tr>";
            $modal_body .= "<td><b>Estado:</b></td>";
            $modal_body .= "<td>".$entrega['estado']."</td>";
            $modal_body .= "</tr>";
            $modal_body .= "<tr>";    
            $modal_body .= "<td><b>CEP:</b></td>";
            $modal_body .= "<td>".$entrega['cep']."</td>";
            $modal_body .= "</tr>";
        }
        if($venda['forma_pagamento'] == 'Boleto'){
            $modal_body .= "<tr>";
            $modal_body .= "<td colspan='2' class='text-center'><b>INFORMAÇÕES DO BOLETO</b></td>";
            $modal_body .= "</tr>";
            $modal_body .= "<tr>";
            $modal_body .= "<td><b>Link do boleto:</b></td>";
            $modal_body .= "<td>".$venda['link_boleto']."</td>";
            $modal_body .= "</tr>";
            $modal_body .= "<tr>";
            $modal_body .= "<td><b>Linha digitável do boleto:</b></td>";
            $modal_body .= "<td>".$venda['linha_digitavel_boleto']."</td>";
            $modal_body .= "</tr>";
        }
        $modal_body .= "</thead>";
        $modal_body .= "</table>";
        $modal_body .= "</div>";
        $modal_body .= "</div>";

        return response()->json($modal_body);
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

            $retorno = [
                'sucesso' => 'Estorno realizado com sucesso!'
            ];
        }
        else{
            $retorno = [
                'erro' => 'id não encontrado!'
            ];
        }

        return response()->json($retorno);
    }

}
