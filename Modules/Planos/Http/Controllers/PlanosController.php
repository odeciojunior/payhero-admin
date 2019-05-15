<?php

namespace Modules\Planos\Http\Controllers;

use App\Foto;
use App\Cupom;
use App\Pixel;
use App\Plano;
use App\Brinde;
use App\Layout;
use App\Produto;
use App\ZenviaSms;
use App\PlanoBrinde;
use App\Entities\UserProjeto;
use App\DadosHotZapp;
use App\ProdutoPlano;
use App\ProjetoProduto;
use App\Transportadora;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class PlanosController extends Controller {

    public function index() {

        return view('planos::index'); 
    }

    public function cadastrarPlano(Request $request){

        $dados = $request->all();
        $dados['projeto'] = Hashids::decode($dados['projeto'])[0];

        $userProjeto = UserProjeto::where([
            ['projeto',$dados['projeto']],
            ['tipo','produtor']
        ])->first();

        $dados['empresa'] = $userProjeto->empresa;
        $dados['preco'] = $this->getValor($dados['preco']);

        $novoCodigoIdentificador = false;

        while($novoCodigoIdentificador == false){

            $codigoIdentificador = $this->randString(3).rand(100,999);
            $plano = Plano::where('cod_identificador', $codigoIdentificador)->first();
            if($plano == null){
                $novoCodigoIdentificador = true;
                $dados['cod_identificador'] = $codigoIdentificador;
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

        $qtdProduto = 1;

        while(isset($dados['produto_'.$qtdProduto]) && $dados['produto_'.$qtdProduto] != ''){

            ProdutoPlano::create([
                'produto'            => $dados['produto_'.$qtdProduto],
                'plano'              => $plano->id,
                'quantidade_produto' => $dados['produto_qtd_'.$qtdProduto++]
            ]);
        }

        $qtdBrinde = 1;

        while(isset($dados['brinde_'.$qtdBrinde]) && $dados['brinde_'.$qtdBrinde] != ''){

            PlanoBrinde::create([
                'brinde' => $dados['brinde_'.$qtdBrinde++],
                'plano'  => $plano->id,
            ]);
        }


        return response()->json('sucesso');
    }

    public function updatePlano(Request $request){

        $dados = $request->all();

        unset($dados['projeto']);

        $dados['preco'] = $this->getValor($dados['preco']);

        $plano = Plano::where('id',Hashids::decode($dados['id']))->first();

        $plano->update($dados);

        $foto = $request->file('foto_plano_editar');

        if ($foto != null) {
            $nome_foto = 'plano_' . $plano['id'] . '_.' . $foto->getClientOriginalExtension();

            Storage::delete('public/upload/plano/'.$nome_foto);
 
            $foto->move(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO, $nome_foto);

            $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $nome_foto);

            $img->crop($dados['foto_plano_editar_w'], $dados['foto_plano_editar_h'], $dados['foto_plano_editar_x1'], $dados['foto_plano_editar_y1']);

            $img->resize(200, 200);

            Storage::delete('public/upload/plano/'.$nome_foto);

            $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $nome_foto);

            $plano->update([
                'foto' => $nome_foto,
            ]);
        }

        $produtosPlanos = ProdutoPlano::where('plano', $plano['id'])->get()->toArray();
        if(count($produtosPlanos) > 0){
            foreach($produtosPlanos as $produto_plano){
                ProdutoPlano::find($produto_plano['id'])->delete();
            }
        }

        $planosBrindes = PlanoBrinde::where('plano', $plano['id'])->get()->toArray();
        if(count($planosBrindes) > 0){
            foreach($planosBrindes as $plano_brinde){
                PlanoBrinde::find($plano_brinde['id'])->delete();
            }
        }

        $qtdProduto = 1;

        while(isset($dados['produto_'.$qtdProduto]) && $dados['produto_'.$qtdProduto] != ''){

            ProdutoPlano::create([
                'produto'            => $dados['produto_'.$qtdProduto],
                'plano'              => $plano->id,
                'quantidade_produto' => $dados['produto_qtd_'.$qtdProduto++]
            ]);
        }

        $qtdBrinde = 1;

        while(isset($dados['brinde_'.$qtdBrinde]) && $dados['brinde_'.$qtdBrinde] != ''){

            PlanoBrinde::create([
                'brinde' => $dados['brinde_'.$qtdBrinde++],
                'plano'  => $plano->id,
            ]);
        }

        return response()->json('sucesso');
    }

    public function deletarPlano(Request $request){

        $dados = $request->all();

        $servico_sms = ZenviaSms::where('plano',$dados['id'])->first();

        if($servico_sms != null){
            return response()->json('Impossível excluir, possui serviço de sms integrado.');            
        }

        $plano = Plano::where('id',Hashids::decode($dados['id']))->first();

        $fotos = Foto::where('plano', $plano['id'])->get()->toArray();

        if(count($fotos) > 0){
            foreach($fotos as $foto){
                Foto::find($foto['id'])->delete();
            }
        }

        $produtosPlanos = ProdutoPlano::where('plano', $plano['id'])->get()->toArray();
        if(count($produtosPlanos) > 0){
            foreach($produtosPlanos as $produto_plano){
                ProdutoPlano::find($produto_plano['id'])->delete();
            }
        }

        $planosBrindes = PlanoBrinde::where('plano', $plano['id'])->get()->toArray();
        if(count($planosBrindes) > 0){
            foreach($planosBrindes as $plano_brinde){
                PlanoBrinde::find($plano_brinde['id'])->delete();
            }
        }

        $plano->delete();

        return response()->json('sucesso');

    }

    public function dadosPlano(Request $request) {

        $dados = $request->all();

        $planos = \DB::table('planos as plano')
                      ->whereNull('deleted_at');

        if(isset($dados['projeto'])){
            $planos = $planos->where('plano.projeto','=', Hashids::decode($dados['projeto']));
        }
        else{
            return response()->json('projeto não encontrado');
        }

        $planos = $planos->get([
                'plano.id',
                'plano.nome',
                'plano.descricao',
                'plano.cod_identificador',
                'plano.preco',
        ]);
 
        return Datatables::of($planos)
        ->addColumn('detalhes', function ($plano) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_plano' data-placement='top' data-toggle='tooltip' title='Detalhes' plano='".Hashids::encode($plano->id)."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a class='btn btn-outline btn-primary editar_plano' data-placement='top' data-toggle='tooltip' title='Editar' plano='".Hashids::encode($plano->id)."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_plano' data-placement='top' data-toggle='tooltip' title='Excluir' plano='".Hashids::encode($plano->id)."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function getDetalhesPlano(Request $request){

        $dados = $request->all();

        $plano = Plano::where('id',Hashids::decode($dados['id_plano']))->first();

        $modalBody = '';

        $modalBody .= "<div class='col-xl-12 col-lg-12'>";
        $modalBody .= "<table class='table table-bordered table-hover table-striped'>";
        $modalBody .= "<thead>";
        $modalBody .= "</thead>";
        $modalBody .= "<tbody>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Nome:</b></td>";
        $modalBody .= "<td>".$plano->nome."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Descrição:</b></td>";
        $modalBody .= "<td>".$plano->descricao."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Código identificador:</b></td>";
        $modalBody .= "<td>".$plano->cod_identificador."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Status:</b></td>";
        if($plano->status == 1)
            $modalBody .= "<td>Ativo</td>";
        else
            $modalBody .= "<td>Inativo</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Preço:</b></td>";
        $modalBody .= "<td>".$plano->preco."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Status cupons:</b></td>";
        if($plano->status_cupom == 1)
            $modalBody .= "<td>Cupons ativos</td>";
        else
            $modalBody .= "<td>Cupons não ativos</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Possui frete:</b></td>";
        if($plano->frete == 1)
            $modalBody .= "<td>Sim</td>";
        else
            $modalBody .= "<td>Não</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Frete fixo:</b></td>";
        if($plano->frete_fixo == 1)
            $modalBody .= "<td>Sim</td>";
        else
            $modalBody .= "<td>Não</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Valor frete fixo:</b></td>";
        $modalBody .= "<td>".$plano->valor_frete."</td>";
        $modalBody .= "</tr>";


        $produtosPlano = ProdutoPlano::where('plano',$plano->id)->get()->toArray();

        if(count($produtosPlano) > 0){

            $modalBody .= "<tr class='text-center'>";
            $modalBody .= "<td colspan='2'><b>Produtos do plano:</b></td>";
            $modalBody .= "</tr>";
    
            foreach($produtosPlano as $produtoPlano){

                $produto = Produto::find($produtoPlano['produto']);
                $modalBody .= "<tr>";
                $modalBody .= "<td><b>Produto:</b></td>";
                $modalBody .= "<td>".$produto->nome."</td>";
                $modalBody .= "</tr>";
                $modalBody .= "<tr>";
                $modalBody .= "<td><b>Quantidade:</b></td>";
                $modalBody .= "<td>".$produtoPlano['quantidade_produto']."</td>";
                $modalBody .= "</tr>";
            }
        }

        $planoBrindes = PlanoBrinde::where('plano',$plano->id)->get()->toArray();

        if(count($planoBrindes) > 0){

            $modalBody .= "<tr class='text-center'>";
            $modalBody .= "<td colspan='2'><b>Brindes do plano:</b></td>";
            $modalBody .= "</tr>";
    
            foreach($planoBrindes as $planoBrinde){

                $brinde = Brinde::find($planoBrinde['brinde']);

                $modalBody .= "<tr>";
                $modalBody .= "<td><b>Brinde:</b></td>";
                $modalBody .= "<td>".$brinde->descricao."</td>";
                $modalBody .= "</tr>";
            }
        }

        $modalBody .= "</thead>";
        $modalBody .= "</table>";
        $modalBody .= "<div class='text-center'>";
        if(!$plano->shopify_id){
            $modalBody .= "<img src='".url(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO.$plano->foto)."?dummy=".uniqid()."' style='height: 250px'>";
        }
        else{
            $modalBody .= "<img src='".$plano->foto."' style='height: 250px'>";
        }
        $modalBody .= "</div>";
        $modalBody .= "</div>";

        return response()->json($modalBody);
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

        $arrayValor = explode('.',$str);

        if(count($arrayValor) == 1){
            $str = $str.'.00';
        }
        else{
            if(strlen($arrayValor['1']) == 1){
                $str .= '0';
            }
        }
       
        return $str;
    } 

    public function getFormAddPlano(Request $request){

        $dados = $request->all();

        $transportadoras = Transportadora::all();

        $produtos = Produto::where('user', \Auth::user()->id)->get()->toArray();

        $brindes = Brinde::where('projeto',$dados['projeto'])->get()->toArray();

        $dados_hotzapp = DadosHotZapp::all(); 

        $form = view('planos::cadastro',[
            'transportadoras' => $transportadoras,
            'produtos'        => $produtos,
            'brindes'         => $brindes,
            'dados_hotzapp'   => $dados_hotzapp,
        ]);

        return response()->json($form->render());
    }

    public function getFormEditarPlano(Request $request){

        $dados = $request->all();

        $plano = Plano::where('id',Hashids::decode($dados['id']))->first();

        $idPlano = Hashids::encode($plano->id);

        $transportadoras = Transportadora::all();

        $produtos = Produto::where('user',\Auth::user()->id)->get()->toArray();

        $brindes = Brinde::where('projeto',$dados['projeto'])->get()->toArray();

        $dados_hotzapp = DadosHotZapp::all();

        $produtosPlanos = ProdutoPlano::where('plano', $plano['id'])->get()->toArray();

        $planoBrindes = PlanoBrinde::where('plano', $plano['id'])->get()->toArray();

        $caminho_foto = url(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO.$plano['foto']."?dummy=".uniqid());

        $form = view('planos::editar',[
            'id_plano' => $idPlano,
            'plano' => $plano,
            'transportadoras' => $transportadoras,
            'foto' => $caminho_foto,
            'produtos_planos' => $produtosPlanos,
            'produtos' => $produtos,
            'brindes' => $brindes,
            'planoBrindes' => $planoBrindes,
        ]);

        return response()->json($form->render());

    }

}
