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
use App\PlanoCupom;
use App\PlanoPixel;
use App\PlanoBrinde;
use App\UserProjeto;
use App\DadosHotZapp;
use App\ProdutoPlano;
use App\ProjetoProduto;
use App\Transportadora;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class PlanosController extends Controller {

    public function index() {

        return view('planos::index'); 
    }

    public function cadastro() {

        $transportadoras = Transportadora::all();
        $produtos = Produto::all();
        $pixels = Pixel::all();
        $brindes = Brinde::all();
        $cupons = Cupom::all();
        $dados_hotzapp = DadosHotZapp::all(); 
        $layouts = Layout::all();

        return view('planos::cadastro',[
            'transportadoras' => $transportadoras,
            'produtos' => $produtos,
            'pixels' => $pixels,
            'brindes' => $brindes,
            'cupons' => $cupons,
            'dados_hotzapp' => $dados_hotzapp,
            'layouts' => $layouts,
        ]);
    }

    public function cadastrarPlano(Request $request){

        $dados = $request->all();

        $user_projeto = UserProjeto::where([
            ['projeto',$dados['projeto']],
            ['tipo','produtor']
        ])->first();

        $dados['empresa'] = $user_projeto->empresa;
        $dados['preco'] = $this->getValor($dados['preco']);
        $dados['valor_frete'] = $this->getValor($dados['valor_frete']);

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

        $qtd_pixel = 1;

        while(isset($dados['pixel_'.$qtd_pixel]) && $dados['pixel_'.$qtd_pixel] != ''){

            PlanoPixel::create([
                'pixel' => $dados['pixel_'.$qtd_pixel++],
                'plano' => $plano->id,
            ]);
        }

        $qtd_cupom = 1;

        while(isset($dados['cupom_'.$qtd_cupom]) && $dados['cupom_'.$qtd_cupom] != ''){

            PlanoCupom::create([
                'cupom' => $dados['cupom_'.$qtd_cupom++],
                'plano' => $plano->id,
            ]);
        }

        return response()->json('sucesso');
    }

    public function editarPlano($id){

        $plano = Plano::find($id);
        $transportadoras = Transportadora::all();

        $produtos = Produto::all();
        $produtosPlanos = ProdutoPlano::where('plano', $plano['id'])->get()->toArray();

        $pixels = Pixel::all();
        $planoPixels = PlanoPixel::where('plano', $plano['id'])->get()->toArray();

        $cupons = Cupom::all();
        $planoCupons = PlanoCupom::where('plano', $plano['id'])->get()->toArray();

        $brindes = Brinde::all();
        $planoBrindes = PlanoBrinde::where('plano', $plano['id'])->get()->toArray();

        $layouts = Layout::all();

        if($foto != null){
            $caminho_foto = url(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO.$plano['foto']);
        }
        else{
            $caminho_foto = null;
        }

        return view('planos::editar',[
            'plano' => $plano,
            'transportadoras' => $transportadoras,
            'foto' => $caminho_foto,
            'produtos_planos' => $produtosPlanos,
            'produtos' => $produtos,
            'pixels' => $pixels,
            'planoPixels' => $planoPixels,
            'cupons' => $cupons,
            'planoCupons' => $planoCupons,
            'brindes' => $brindes,
            'planoBrindes' => $planoBrindes,
            'layouts' => $layouts,
        ]);

    }

    public function updatePlano(Request $request){

        $dados = $request->all();

        $dados['preco'] = $this->getValor($dados['preco']);
        $dados['valor_frete'] = $this->getValor($dados['valor_frete']);

        $plano = Plano::find($dados['id']);
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

        $produtos_planos = ProdutoPlano::where('plano', $plano['id'])->get()->toArray();
        if(count($produtos_planos) > 0){
            foreach($produtos_planos as $produto_plano){
                ProdutoPlano::find($produto_plano['id'])->delete();
            }
        }

        $planos_brindes = PlanoBrinde::where('plano', $plano['id'])->get()->toArray();
        if(count($planos_brindes) > 0){
            foreach($planos_brindes as $plano_brinde){
                PlanoBrinde::find($plano_brinde['id'])->delete();
            }
        }

        $planos_pixels = PlanoPixel::where('plano', $plano['id'])->get()->toArray();
        if(count($planos_pixels) > 0){
            foreach($planos_pixels as $plano_pixel){
                PlanoPixel::find($plano_pixel['id'])->delete();
            }
        }

        $planos_cupons = PlanoCupom::where('plano', $plano['id'])->get()->toArray();
        if(count($planos_cupons) > 0){
            foreach($planos_cupons as $plano_cupom){
                PlanoCupom::find($plano_cupom['id'])->delete();
            }
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

        $qtd_pixel = 1;

        while(isset($dados['pixel_'.$qtd_pixel]) && $dados['pixel_'.$qtd_pixel] != ''){

            PlanoPixel::create([
                'pixel' => $dados['pixel_'.$qtd_pixel++],
                'plano' => $plano->id,
            ]);
        }

        $qtd_cupom = 1;

        while(isset($dados['cupom_'.$qtd_cupom]) && $dados['cupom_'.$qtd_cupom] != ''){

            PlanoCupom::create([
                'cupom' => $dados['cupom_'.$qtd_cupom++],
                'plano' => $plano->id,
            ]);
        }
        
        return redirect()->route('planos');
    }

    public function deletarPlano(Request $request){

        $dados = $request->all();

        $servico_sms = ZenviaSms::where('plano',$dados['id'])->first();

        if($servico_sms != null){
            return response()->json('Impossível excluir, possui serviço de sms integrado.');            
        }

        $plano = Plano::find($dados['id']); 

        $fotos = Foto::where('plano', $plano['id'])->get()->toArray();

        if(count($fotos) > 0){
            foreach($fotos as $foto){
                Foto::find($foto['id'])->delete();
            }
        }

        $produtos_planos = ProdutoPlano::where('plano', $plano['id'])->get()->toArray();
        if(count($produtos_planos) > 0){
            foreach($produtos_planos as $produto_plano){
                ProdutoPlano::find($produto_plano['id'])->delete();
            }
        }

        $planos_brindes = PlanoBrinde::where('plano', $plano['id'])->get()->toArray();
        if(count($planos_brindes) > 0){
            foreach($planos_brindes as $plano_brinde){
                PlanoBrinde::find($plano_brinde['id'])->delete();
            }
        }

        $planos_pixels = PlanoPixel::where('plano', $plano['id'])->get()->toArray();
        if(count($planos_pixels) > 0){
            foreach($planos_pixels as $plano_pixel){
                PlanoPixel::find($plano_pixel['id'])->delete();
            }
        }

        $planos_cupons = PlanoCupom::where('plano', $plano['id'])->get()->toArray();
        if(count($planos_cupons) > 0){
            foreach($planos_cupons as $plano_cupom){
                PlanoCupom::find($plano_cupom['id'])->delete();
            }
        }

        $plano->delete();

        return response()->json('sucesso');

    }

    public function dadosPlano(Request $request) {

        $dados = $request->all();

        $planos = \DB::table('planos as plano');

        if(isset($dados['projeto'])){
            $planos = $planos->where('plano.projeto','=', $dados['projeto']);
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
                        <a class='btn btn-outline btn-success detalhes_plano' data-placement='top' data-toggle='tooltip' title='Detalhes' plano='".$plano->id."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a class='btn btn-outline btn-primary editar_plano' data-placement='top' data-toggle='tooltip' title='Editar' plano='".$plano->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_plano' data-placement='top' data-toggle='tooltip' title='Excluir' plano='".$plano->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function getDetalhesPlano(Request $request){

        $dados = $request->all();

        $plano = Plano::find($dados['id_plano']);

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Nome:</b></td>";
        $modal_body .= "<td>".$plano->nome."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Descrição:</b></td>";
        $modal_body .= "<td>".$plano->descricao."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Código identificador:</b></td>";
        $modal_body .= "<td>".$plano->cod_identificador."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Status:</b></td>";
        if($plano->status == 1)
            $modal_body .= "<td>Ativo</td>";
        else
            $modal_body .= "<td>Inativo</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Preço:</b></td>";
        $modal_body .= "<td>".$plano->preco."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Status cupons:</b></td>";
        if($plano->status_cupom == 1)
            $modal_body .= "<td>Cupons ativos</td>";
        else
            $modal_body .= "<td>Cupons não ativos</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Possui frete:</b></td>";
        if($plano->frete == 1)
            $modal_body .= "<td>Sim</td>";
        else
            $modal_body .= "<td>Não</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Frete fixo:</b></td>";
        if($plano->frete_fixo == 1)
            $modal_body .= "<td>Sim</td>";
        else
            $modal_body .= "<td>Não</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Valor frete fixo:</b></td>";
        $modal_body .= "<td>".$plano->valor_frete."</td>";
        $modal_body .= "</tr>";


        $produtosPlano = ProdutoPlano::where('plano',$plano->id)->get()->toArray();

        if(count($produtosPlano) > 0){

            $modal_body .= "<tr class='text-center'>";
            $modal_body .= "<td colspan='2'><b>Produtos do plano:</b></td>";
            $modal_body .= "</tr>";
    
            foreach($produtosPlano as $produtoPlano){

                $produto = Produto::find($produtoPlano['produto']);
                $modal_body .= "<tr>";
                $modal_body .= "<td><b>Produto:</b></td>";
                $modal_body .= "<td>".$produto->nome."</td>";
                $modal_body .= "</tr>";
                $modal_body .= "<tr>";
                $modal_body .= "<td><b>Quantidade:</b></td>";
                $modal_body .= "<td>".$produtoPlano['quantidade_produto']."</td>";
                $modal_body .= "</tr>";
            }
        }

        $planoBrindes = PlanoBrinde::where('plano',$plano->id)->get()->toArray();

        if(count($planoBrindes) > 0){

            $modal_body .= "<tr class='text-center'>";
            $modal_body .= "<td colspan='2'><b>Brindes do plano:</b></td>";
            $modal_body .= "</tr>";
    
            foreach($planoBrindes as $planoBrinde){

                $brinde = Brinde::find($planoBrinde['brinde']);

                $modal_body .= "<tr>";
                $modal_body .= "<td><b>Brinde:</b></td>";
                $modal_body .= "<td>".$brinde->descricao."</td>";
                $modal_body .= "</tr>";
            }
        }

        $planoPixels = PlanoPixel::where('plano',$plano->id)->get()->toArray();

        if(count($planoPixels) > 0){

            $modal_body .= "<tr class='text-center'>";
            $modal_body .= "<td colspan='2'><b>Pixels do plano:</b></td>";
            $modal_body .= "</tr>";

            foreach($planoPixels as $planoPixel){

                $pixel = Pixel::find($planoPixel['pixel']);

                $modal_body .= "<tr>";
                $modal_body .= "<td><b>Pixel:</b></td>";
                $modal_body .= "<td>".$pixel->nome."</td>";
                $modal_body .= "</tr>";
            }
        }

        $planoCupons = PlanoCupom::where('plano',$plano->id)->get()->toArray();

        if(count($planoCupons) > 0){

            $modal_body .= "<tr class='text-center'>";
            $modal_body .= "<td colspan='2'><b>Cupons do plano:</b></td>";
            $modal_body .= "</tr>";

            foreach($planoCupons as $planoCupom){

                $cupom = Cupom::find($planoCupom['cupom']);

                $modal_body .= "<tr>";
                $modal_body .= "<td><b>Cupom:</b></td>";
                $modal_body .= "<td>".$cupom->nome."</td>";
                $modal_body .= "</tr>";
            }
        }

        $modal_body .= "</thead>";
        $modal_body .= "</table>";
        $modal_body .= "<div class='text-center'>";
        $modal_body .= "<img src='".url(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO.$plano->foto)."?dummy=".uniqid()."' style='height: 250px'>";
        $modal_body .= "</div>";
        $modal_body .= "</div>";

        return response()->json($modal_body);
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

    public function getFormAddPlano(Request $request){

        $dados = $request->all();

        $transportadoras = Transportadora::all();

        $produtos_projeto = ProjetoProduto::where('projeto',$dados['projeto'])->get()->toArray();

        $produtos = Produto::where('user', \Auth::user()->id)->get()->toArray();

        $pixels = Pixel::where('projeto',$dados['projeto'])->get()->toArray();

        $brindes = Brinde::where('projeto',$dados['projeto'])->get()->toArray();

        $cupons = Cupom::where('projeto',$dados['projeto'])->get()->toArray();

        $dados_hotzapp = DadosHotZapp::all(); 

        $form = view('planos::cadastro',[
            'transportadoras' => $transportadoras,
            'produtos' => $produtos,
            'pixels' => $pixels,
            'brindes' => $brindes,
            'cupons' => $cupons,
            'dados_hotzapp' => $dados_hotzapp,
        ]);

        return response()->json($form->render());
    }

    public function getFormEditarPlano(Request $request){

        $dados = $request->all();

        $plano = Plano::find($dados['id']);
        $transportadoras = Transportadora::all();

        $produtos_projeto = ProjetoProduto::where('projeto',$dados['projeto'])->get()->toArray();

        $produtos = Produto::where('user',\Auth::user()->id)->get()->toArray();

        $pixels = Pixel::where('projeto',$dados['projeto'])->get()->toArray();

        $brindes = Brinde::where('projeto',$dados['projeto'])->get()->toArray();

        $cupons = Cupom::where('projeto',$dados['projeto'])->get()->toArray();

        $dados_hotzapp = DadosHotZapp::all();

        $produtosPlanos = ProdutoPlano::where('plano', $plano['id'])->get()->toArray();

        $planoPixels = PlanoPixel::where('plano', $plano['id'])->get()->toArray();

        $planoCupons = PlanoCupom::where('plano', $plano['id'])->get()->toArray();

        $planoBrindes = PlanoBrinde::where('plano', $plano['id'])->get()->toArray();

        $caminho_foto = url(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO.$plano['foto']."?dummy=".uniqid());

        $form = view('planos::editar',[
            'plano' => $plano,
            'transportadoras' => $transportadoras,
            'foto' => $caminho_foto,
            'produtos_planos' => $produtosPlanos,
            'produtos' => $produtos,
            'pixels' => $pixels,
            'planoPixels' => $planoPixels,
            'cupons' => $cupons,
            'planoCupons' => $planoCupons,
            'brindes' => $brindes,
            'planoBrindes' => $planoBrindes,
        ]);

        return response()->json($form->render());

    }

}
