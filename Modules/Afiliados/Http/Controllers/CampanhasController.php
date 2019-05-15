<?php

namespace Modules\Afiliados\Http\Controllers;

use App\Entities\User;
use App\Pixel;
use App\Plano;
use App\Dominio;
use App\Projeto;
use App\Afiliado;
use App\Campanha;
use Carbon\Carbon;
use App\PlanoVenda;
use App\Entities\UserProjeto;
use App\LinkAfiliado;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\DataTables\Facades\DataTables;

class CampanhasController extends Controller {


    public function getDadosCampanhas(Request $request){

        $dados = $request->all();

        $campanhas = \DB::table('campanhas as campanha')
        ->get([
            'id',
            'descricao',
            'afiliado',
        ])
        ->where('afiliado',Hashids::decode($dados['afiliado'])[0]);

        return Datatables::of($campanhas)
        ->addColumn('qtd_cliques', function ($campanha) {
            $links_afiliado = LinkAfiliado::where('campanha',$campanha->id)->get()->toArray();
            if(count($links_afiliado) < 1){
                return "0";
            }
            $qtd_cliques = 0;
            foreach($links_afiliado as $link_afiliado){
                $qtd_cliques += $link_afiliado['qtd_cliques'];
            }
            return $qtd_cliques;
        })
        ->addColumn('detalhes', function ($campanha) {
            return "<span data-toggle='modal' data-target='#modal_dados_campanha'>
                        <a class='btn btn-outline btn-success dados_campanha' data-placement='top' data-toggle='tooltip' title='Dados da campanha' campanha='".Hashids::encode($campanha->id)."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                            Dados da campanha
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);

    }

    public function campanha(Request $request){

        $dados = $request->all();

        $campanha = Campanha::where('id',Hashids::decode($dados['campanha']))->first();;

        $afiliado = Afiliado::find($campanha->afiliado);

        $projeto = Projeto::find($afiliado['projeto']);

        $dominio = Dominio::where('projeto',$afiliado['projeto'])->first();

        $set_coockie_url = "affiliate.".$dominio['dominio']."/"."setcookie/";

        $url_pagina = $set_coockie_url.LinkAfiliado::where([
            ['afiliado', $afiliado['id']],
            ['plano' , null]
        ])->first()['parametro'];

        $projeto_usuario = UserProjeto::where([
            ['projeto',$projeto['id']],
            ['tipo','produtor']
        ])->first();
        $usuario = User::find($projeto_usuario['user']);
        $planos = Plano::where('projeto',$projeto['id'])->get()->toArray();

        foreach($planos as &$plano){
            $plano['lucro'] = number_format($plano['preco'] * $projeto['porcentagem_afiliados'] / 100, 2);
            $plano['url'] = $set_coockie_url.LinkAfiliado::where([
                ['afiliado', $afiliado['id']],
                ['plano' , $plano['id']]
            ])->first()['parametro'];
        }

        $pixels = Pixel::where('campanha',$campanha->id)->get()->toArray();

        $dados_campanha = view('afiliados::campanha',[
            'planos' => $planos,
            'url_pagina' => $url_pagina,
            'projeto' => $projeto,
            'pixels' => $pixels,
            'id_campanha' => $campanha->id
        ]);

        return response()->json($dados_campanha->render());

    }

    public function cadastrar(Request $request){

        $dados = $request->all();

        $afiliado = Afiliado::where('id',Hashids::decode($dados['afiliado']))->first();

        $projeto = Projeto::find($afiliado->projeto);

        $planos = Plano::where('projeto',$projeto['id'])->get()->toArray();

        $dados['afiliado'] = $afiliado->id;

        $campanha = Campanha::create($dados);

        LinkAfiliado::create([
            'afiliado' => $afiliado->id,
            'parametro' => $this->randString(12),
            'campanha' => $campanha->id
        ]);

        if($projeto['url_cookies_checkout']){
            foreach($planos as $plano){
                LinkAfiliado::create([
                    'afiliado' => $afiliado->id,
                    'parametro' => $this->randString(12),
                    'plano' => $plano['id'],
                    'campanha' => $campanha->id
                ]);
            }
        }

        return response()->json('sucesso');
    }

    public function vendas(Request $request){

        $dados = $request->all();

        $vendas = \DB::table('vendas')
            ->leftjoin('compradores as comprador', 'comprador.id', '=', 'vendas.comprador')
            ->where('vendas.afiliado',Hashids::decode($dados['afiliado']))
            ->get([
                'vendas.id',
                'comprador.nome as comprador',
                'vendas.forma_pagamento as forma',
                'vendas.pagamento_status as status',
                'vendas.data_inicio as data',
                'vendas.data_finalizada as pagamento',
                'vendas.valor_total_pago as valor_total',
                'vendas.valor_frete',
                'vendas.afiliado',
        ]);

        return Datatables::of($vendas)
        ->addColumn('descricao', function ($venda) {
            $planos_venda = PlanoVenda::where('venda',$venda->id)->get()->toArray();
            if(count($planos_venda) > 1){
                return "Carrinho";
            }
            foreach($planos_venda as $plano_venda){
                $plano = Plano::find($plano_venda['plano']);
                return substr($plano['nome'],0,25);
            }
        })
        ->addColumn('valor_liquido', function ($venda) {
            $valor_frete = str_replace('.','',$venda->valor_frete);
            if($valor_frete == ''){
                return $venda->valor_total;
            }
            $valor_liquido = str_replace('.','',$venda->valor_total) - $valor_frete;
            return substr_replace($valor_liquido, '.',strlen($valor_liquido) - 2, 0 );
        })
        ->editColumn('data', function ($venda) {
            return $venda->data ? with(new Carbon($venda->data))->format('d/m/Y H:i:s') : '';
        })
        ->editColumn('pagamento', function ($venda) {
            return $venda->pagamento ? with(new Carbon($venda->pagamento))->format('d/m/Y H:i:s') : '';
        })
        ->editColumn('forma', function ($venda) {
            if($venda->forma == 'Cartão de crédito') 
                return 'Cartão';
            if($venda->forma == 'boleto') 
                return 'Boleto';
            return $venda->forma;
        })
        ->editColumn('status', function ($venda) {
            if($venda->status == 'paid')
                return "<span class='badge badge-round badge-success'>Aprovada</span>";
            if($venda->status == 'refused')
                return "<span class='badge badge-round badge-danger'>Rejeitada</span>";
            if($venda->status == 'waiting_payment')
                return "<span class='badge badge-round badge-info'>Aguardando pagamento</span>";
            if($venda->status == 'refunded')
                return "<span class='badge badge-round badge-default'>Estornada</span>";
            if($venda->status == '')
                return "<span class='badge-round badge-info'>- - - -</span>";
            return $venda->status;
        })
        ->addColumn('detalhes', function ($venda) {
            $buttons = "<button class='btn btn-sm btn-outline btn-primary detalhes_venda' venda='".$venda->id."' data-target='#modal_detalhes' data-toggle='modal' type='button'>
                            Detalhes
                        </button>";
            return $buttons;
        })
        ->rawColumns(['detalhes','status'])
        ->make(true);

    }

    function randString($size){

        $novo_parametro = false;

        while(!$novo_parametro){

            $basic = 'abcdefghijlmnopqrstuvwxyz0123456789';

            $parametro = '';

            for($count= 0; $size > $count; $count++){
                $parametro.= $basic[rand(0, strlen($basic) - 1)];
            }

            $novo_link = LinkAfiliado::where('parametro', $parametro)->first();

            if($novo_link == null){
                $novo_parametro = true;
            }

        }

        return $parametro;
    }

}
