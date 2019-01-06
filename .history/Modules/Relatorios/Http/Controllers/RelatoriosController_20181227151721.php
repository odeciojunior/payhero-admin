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
            ->leftjoin('planos_vendas as plano_venda', 'plano_venda.venda', '=', 'venda.id')
            ->leftjoin('compradores as comprador', 'comprador.id', '=', 'venda.comprador')
            ->leftjoin('planos as plano', 'plano_venda.plano', '=', 'plano.id')
            ->get([
                'venda.id',
                'plano.nome as plano_nome',
                'comprador.nome',
                'venda.meio_pagamento',
                'venda.forma_pagamento',
                'venda.pagamento_status',
                'venda.data_inicio',
                'venda.data_finalizada',
                'venda.valor_plano',
        ]);

        return Datatables::of($vendas)
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
        $plano_venda = PlanoVenda::where('venda', $venda->id)->first();
        $plano = Plano::find($plano_venda->plano);
        $comprador = Comprador::find($venda->comprador);
        $entrega = Entrega::find($venda->entrega);

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Comprador:</b></td>";
        $modal_body .= "<td>".$comprador->nome."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>CPF:</b></td>";
        $modal_body .= "<td>".$comprador->cpf_cnpj."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Email:</b></td>";
        $modal_body .= "<td>".$comprador->email."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Telefone:</b></td>";
        $modal_body .= "<td>".$comprador->telefone."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Produto:</b></td>";
        $modal_body .= "<td>".$plano->nome."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Preço:</b></td>";
        $modal_body .= "<td>".$plano->preco."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Frete:</b></td>";
        $modal_body .= "<td>".$venda->valor_frete."</td>";
        // $modal_body .= "</tr>";
        // $modal_body .= "<td><b>Valor total:</b></td>";
        // $modal_body .= "<td>".(int) $venda->valor_frete + (int) $plano->preco."</td>";
        // $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Código da transação:</b></td>";
        $modal_body .= "<td>#".$venda->id."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Meio de pagamento:</b></td>";
        $modal_body .= "<td>".$venda->meio_pagamento."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Forma de pagamento:</b></td>";
        $modal_body .= "<td>".$venda->forma_pagamento."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Data da venda:</b></td>";
        $modal_body .= "<td>".(new Carbon($venda->data_inicio))->format('d/m/Y H:i:s')."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Status da venda:</b></td>";
        if($venda->mercado_pago_status == 'approved')
            $modal_body .= "<td>Aprovada</td>";
        elseif($venda->mercado_pago_status == 'rejected')
            $modal_body .= "<td>Rejeitada</td>";
        elseif($venda->mercado_pago_status == 'in_process')
            $modal_body .= "<td>Em processo</td>";
        else
            $modal_body .= "<td>".$venda->mercado_pago_status."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Código do plano:</b></td>";
        $modal_body .= "<td>".$plano->cod_identificador."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>CEP:</b></td>";
        $modal_body .= "<td>".$entrega->cep."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Estado:</b></td>";
        $modal_body .= "<td>".$entrega->estado."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Cidade:</b></td>";
        $modal_body .= "<td>".$entrega->cidade."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Bairro:</b></td>";
        $modal_body .= "<td>".$entrega->bairro."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Rua:</b></td>";
        $modal_body .= "<td>".$entrega->rua."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Numero:</b></td>";
        $modal_body .= "<td>".$entrega->numero."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Ponto de referência:</b></td>";
        $modal_body .= "<td>".$entrega->ponto_referencia."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>id kapsula:</b></td>";
        $modal_body .= "<td>".$entrega->id_kapsula_pedido."</td>";
        $modal_body .= "</tr>";
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
