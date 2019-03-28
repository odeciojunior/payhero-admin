<?php

namespace Modules\Planos\Http\Controllers;

use App\Plano;
use App\Brinde;
use App\Produto;
use Carbon\Carbon;
use App\UserProjeto;
use App\PlanoBrinde;
use App\ProdutoPlano;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Helpers\CaminhoArquivosHelper;
use Modules\Planos\Transformers\PlanosResource;

class PlanosApiController extends Controller {

    public function index(Request $request) {

        $planos = Plano::where('projeto', Hashids::decode($request->id_projeto));

        return PlanosResource::collection($planos->paginate(10));
    }

    public function store(Request $request) {

        $dados = $request->all();
        $dados['projeto'] = Hashids::decode($request->id_projeto);

        $user_projeto = UserProjeto::where([
            ['projeto',Hashids::decode($request->id_projeto)],
            ['tipo','produtor']
        ])->first();

        $dados['empresa'] = $user_projeto->empresa;
        $dados['preco'] = $this->getValor($dados['preco']);

        $novo_codigo_identificador = false;

        while($novo_codigo_identificador == false){

            $codigo_identificador = $this->randString(3).rand(100,999);
            $plano = Plano::where('cod_identificador', $codigo_identificador)->first();
            if($plano == null){
                $novo_codigo_identificador = true;
                $dados['cod_identificador'] = $codigo_identificador;
            }
        }

        $plano = Plano::create($dados);

        $foto = $request->file('foto_plano_cadastrar');

        if ($foto != null) {
            $nome_foto = 'plano_' . $plano->id . '_.' . $foto->getClientOriginalExtension();

            Storage::delete('public/upload/plano/'.$nome_foto);
 
            $foto->move(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO, $nome_foto);

            $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $nome_foto);

            $img->crop($dados['foto_plano_cadastrar_w'], $dados['foto_plano_cadastrar_h'], $dados['foto_plano_cadastrar_x1'], $dados['foto_plano_cadastrar_y1']);

            $img->resize(200, 200);

            Storage::delete('public/upload/plano/'.$nome_foto);
            
            $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $nome_foto);

            $plano->update([
                'foto' => $nome_foto,
            ]);
        }

        $qtd_produto = 1;

        while(isset($dados['produto_'.$qtd_produto]) && $dados['produto_'.$qtd_produto] != ''){

            ProdutoPlano::create([
                'produto' => $dados['produto_'.$qtd_produto],
                'plano' => $plano->id,
                'quantidade_produto' => $dados['produto_qtd_'.$qtd_produto++]
            ]);
        }

        $qtd_brinde = 1;

        while(isset($dados['brinde_'.$qtd_brinde]) && $dados['brinde_'.$qtd_brinde] != ''){

            PlanoBrinde::create([
                'brinde' => $dados['brinde_'.$qtd_brinde++],
                'plano' => $plano->id,
            ]);
        }

        return response()->json('sucesso');
    }

    public function show(Request $request) {

        $plano = Plano::find(Hashids::decode($request->id_plano));

        $dados = [];
        $dados['nome'] = $plano['nome'];
        $dados['descricao'] = $plano['descricao'];
        $dados['cod_identificador'] = $plano['cod_identificador'];
        $dados['status'] = $plano['status'];
        $dados['preco'] = $plano['preco'];
        $dados['frete'] = $plano['frete'];
        $dados['frete_fixo'] = $plano['frete_fixo'];
        $dados['valor_frete'] = $plano['valor_frete'];
        $dados['created_at'] = with(new Carbon($plano->created_at))->format('d/m/Y H:i:s');
        $dados['foto'] = url(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO.$plano->foto)."?dummy=".uniqid();

        $produtosPlano = ProdutoPlano::where('plano',$plano->id)->get()->toArray();
        if(count($produtosPlano) > 0){

            $produtos = [];
            foreach($produtosPlano as $produtoPlano){

                $produto = Produto::find($produtoPlano['produto']);
                $produtos[] = [
                    'nome' => $produto['nome'],
                    'quantidade' => $produtoPlano['quantidade_produto']
                ];

            }
            $dados['produtos'] = $produtos;
        }

        $planoBrindes = PlanoBrinde::where('plano',$plano->id)->get()->toArray();

        if(count($planoBrindes) > 0){

            $brindes = [];
            foreach($planoBrindes as $planoBrinde){

                $brinde = Brinde::find($planoBrinde['brinde']);
                $brindes[] = $brinde->descricao;
            }
            $dados['brindes'] = $brindes;
        }

        return response()->json($dados);
    }

    public function update(Request $request) {

        $dados = $request->all();

        Plano::find(Hashids::decode($dados['id']))->update($dados);

        return response()->json('sucesso');
    }

    public function destroy(Request $request) {

        Plano::find(Hashids::decode($request->id_plano))->delete();

        return response()->json('sucesso');
    }

    function randString($size){

        $basic = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $return= "";

        for($count= 0; $size > $count; $count++){

            $return.= $basic[rand(0, strlen($basic) - 1)];
        }

        return $return;
    }

    function getValor($str) {

        if($str == ''){
            return '0.00';
        }

        if(strstr($str, ",")) {
          $str = str_replace(".", "", $str);
          $str = str_replace(",", ".", $str);
        }

        $array_valor = explode('.',$str);

        if(count($array_valor) == 1){
            $str = $str.'.00';
        }
        else{
            if(strlen($array_valor['1']) == 1){
                $str .= '0';
            }
        }
       
        return $str;
    } 

}
