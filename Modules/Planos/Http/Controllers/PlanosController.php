<?php

namespace Modules\Planos\Http\Controllers;

use App\Foto;
use App\Plano;
use App\Pixel;
use App\Cupom;
use App\Brinde;
use App\Produto;
use App\PlanoPixel;
use App\PlanoCupom;
use App\PlanoBrinde;
use App\DadosHotZapp;
use App\ProdutoPlano;
use App\Transportadora;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
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

        return view('planos::cadastro',[
            'transportadoras' => $transportadoras,
            'produtos' => $produtos,
            'pixels' => $pixels,
            'brindes' => $brindes,
            'cupons' => $cupons,
            'dados_hotzapp' => $dados_hotzapp,
        ]);
    }

    public function cadastrarPlano(Request $request){

        $dados = $request->all();

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

        $foto = $request->file('foto');

        if ($foto != null) {
            $nome_foto = 'plano_' . $plano->id . '_.' . $foto->getClientOriginalExtension();

            $foto->move(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO, $nome_foto);

            Foto::create([
                'caminho_imagem' => $nome_foto,
                'plano' => $plano['id'],
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

        return redirect()->route('planos');
    }

    public function editarPlano($id){

        $plano = Plano::find($id);
        $transportadoras = Transportadora::all();
        $foto = Foto::where('plano',$plano['id'])->first();

        $produtos = Produto::all();
        $produtosPlanos = ProdutoPlano::where('plano', $plano['id'])->get()->toArray();

        $pixels = Pixel::all();
        $planoPixels = PlanoPixel::where('plano', $plano['id'])->get()->toArray();

        $cupons = Cupom::all();
        $planoCupons = PlanoCupom::where('plano', $plano['id'])->get()->toArray();

        $brindes = Brinde::all();
        $planoBrindes = PlanoBrinde::where('plano', $plano['id'])->get()->toArray();

        if($foto != null){
            $caminho_foto = url(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO.$foto->caminho_imagem);
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
        ]);

    }

    public function updatePlano(Request $request){

        $dados = $request->all();

        $plano = Plano::find($dados['id']);
        $plano->update($dados);

        $foto = $request->file('foto');

        if ($foto != null) {
            $nome_foto = 'plano_' . $plano->id . '_.' . $foto->getClientOriginalExtension();

            $foto->move(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO, $nome_foto);
        }

        return redirect()->route('planos');
    }

    public function deletarPlano($id){

        $plano = Plano::find($id);

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

        return redirect()->route('planos');

    }

    public function dadosPlano() {

        $planos = \DB::table('planos as plano')
            ->get([
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
                        <a href='/planos/editar/$plano->id' class='btn btn-outline btn-primary editar_plano' data-placement='top' data-toggle='tooltip' title='Editar' plano='".$plano->id."'>
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
        // else{

        //     $modal_body .= "<tr class='text-center'>";
        //     $modal_body .= "<td colspan='2'><b>Plano sem nenhum brinde</b></td>";
        //     $modal_body .= "</tr>";
        // }

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
        // else{

        //     $modal_body .= "<tr class='text-center'>";
        //     $modal_body .= "<td colspan='2'><b>Plano sem nenhum pixel</b></td>";
        //     $modal_body .= "</tr>";
        // }

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
        // else{

        //     $modal_body .= "<tr class='text-center'>";
        //     $modal_body .= "<td colspan='2'><b>Plano sem nenhum cupom de desconto</b></td>";
        //     $modal_body .= "</tr>";
        // }

        $modal_body .= "</thead>";
        $modal_body .= "</table>";
        $foto = Foto::where('plano', $plano->id)->first();
        if($foto != null){
            $modal_body .= "<div class='text-center'>";
            $modal_body .= "<img src='".url(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO.$foto->caminho_imagem)."' style='height: 250px'>";
            $modal_body .= "</div>";
        }
        else
            $modal_body .= "<img src='' alt='Imagem não encontrada'>";
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


}
