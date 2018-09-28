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

        return datatables(\DB::table('vendas as venda')

            ->leftjoin('planos_vendas as plano_venda', 'plano_venda.venda', '=', 'venda.id')
            ->leftjoin('compradores as comprador', 'comprador.id', '=', 'venda.comprador')
            ->leftjoin('planos as plano', 'plano_venda.plano', '=', 'plano.id')

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
            ])
        )->toJson();

    }

}
