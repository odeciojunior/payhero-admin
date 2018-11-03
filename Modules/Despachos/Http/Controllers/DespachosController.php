<?php

namespace Modules\Despachos\Http\Controllers;

use App\Plano;
use App\Venda;
use App\Entrega;
use App\PlanoVenda;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class DespachosController extends Controller
{
    public function index() {

        return view('despachos::index'); 
    }

    public function atualizaEntregas(){

        $vendas = Venda::all();

        foreach($vendas as $venda){

            $entrega = Entrega::find($venda['entrega']);

            if($entrega == null)
                continue;

            $plano_venda = PlanoVenda::where('venda',$venda['id'])->first();

            if($plano_venda == null)
                continue;

            $plano = Plano::find($plano_venda['plano']);

            if($plano == null)
                continue;

            $entrega->update([
                'transportadora' => $plano['transportadora']
            ]);
        }

    }

    public function dadosDespachos() {

        $vendas = \DB::table('vendas as venda')
            ->leftjoin('planos_vendas as plano_venda', 'plano_venda.venda', '=', 'venda.id')
            ->leftjoin('compradores as comprador', 'comprador.id', '=', 'venda.comprador')
            ->leftjoin('planos as plano', 'plano_venda.plano', '=', 'plano.id')
            ->leftjoin('entregas as entrega', 'venda.entrega', '=', 'entrega.id')
            ->leftjoin('transportadoras as transportadora', 'plano.transportadora', '=', 'transportadora.id')
            ->where('entrega.transportadora','2')
            ->where('venda.mercado_pago_status','approved')
            ->get([
                'venda.id',
                'plano.nome as plano_nome',
                'comprador.nome',
                'venda.meio_pagamento',
                'venda.forma_pagamento',
                'venda.mercado_pago_status',
                'venda.data_inicio',
                'venda.data_finalizada',
                'venda.valor_plano',
                'entrega.cod_rastreio as codigo_rastreio'
        ]);

        return Datatables::of($vendas)
        ->addColumn('detalhes', function ($venda) {

            return "<button class='btn btn-sm btn-outline btn-primary detalhes_venda' venda='".$venda->id."' data-target='#modal_detalhes' data-toggle='modal' type='button'>Detalhes</button>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function getDetalhesTransportadora(Request $request){

        $dados = $request->all();

        $transportadora = Transportadora::find($dados['id_transportadora']);

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Status:</b></td>";
        $modal_body .= "<td>".$transportadora->status."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>CNPJ:</b></td>";
        $modal_body .= "<td>".$transportadora->cnpj."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Nome fantasia:</b></td>";
        $modal_body .= "<td>".$transportadora->nome."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Email:</b></td>";
        $modal_body .= "<td>".$transportadora->email."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Telefone:</b></td>";
        $modal_body .= "<td>".$transportadora->telefone."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>CEP:</b></td>";
        $modal_body .= "<td>".$transportadora->cep."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Estado:</b></td>";
        $modal_body .= "<td>".$transportadora->estado."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Cidade:</b></td>";
        $modal_body .= "<td>".$transportadora->cidade."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Bairro:</b></td>";
        $modal_body .= "<td>".$transportadora->bairro."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Rua:</b></td>";
        $modal_body .= "<td>".$transportadora->logradouro."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Número:</b></td>";
        $modal_body .= "<td>".$transportadora->numero."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Complemento:</b></td>";
        $modal_body .= "<td>".$transportadora->complemento."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Atividade principal:</b></td>";
        $modal_body .= "<td>".$transportadora->atividade_principal."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Capital social:</b></td>";
        $modal_body .= "<td>".$transportadora->capital_social."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Data de abertura:</b></td>";
        $modal_body .= "<td>".$transportadora->abertura."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "</thead>";
        $modal_body .= "</table>";
        $modal_body .= "</div>";
        $modal_body .= "</div>";

        return response()->json($modal_body);
    }
}


