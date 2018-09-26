<?php

namespace Modules\Relatorios\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Venda;

class RelatoriosController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function vendas() {

        $vendas = Venda::all()->toArray();

        return view('relatorios::vendas', [
            'vendas' => $vendas
        ]);

    }


    public function dadosVendas(){

        return datatables(Venda::select(
            'status',
            'forma_pagamento',
            'valor_total_pago',
            'valor_recebido_mercado_pago',
            'valor_plano',
            'valor_frete',
            'cod_cupom',
            'meio_pagamento',
            'data_inicio',
            'data_finalizada',
            'comprador',
            'mercado_pago_id',
            'mercado_pago_status',
            'qtd_parcela',
            'bandeira',
            'entrega',
            'valor_cupom',
            'tipo_cupom'
        )->get())->toJson();
    
    }

}
