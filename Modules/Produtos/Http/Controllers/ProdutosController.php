<?php

namespace Modules\Produtos\Http\Controllers;

use App\Empresa;
use App\Produto;
use App\Categoria;
use App\ProjetoProduto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class ProdutosController extends Controller {

    public function index(Request $request) {

        $produtos = Produto::where('user',\Auth::user()->id)->where('shopify', '0');

        if(isset($request->nome)){
            $produtos = $produtos->where('nome','LIKE','%'.$request->nome.'%');
        }
        
        $produtos = $produtos->orderBy('id','DESC')->paginate(12);
 
        return view('produtos::index',[
            'produtos' => $produtos
        ]);
    }

    public function cadastro() {

        $categorias = Categoria::all();

        return view('produtos::cadastro',[
            'categorias' => $categorias,
        ]);
    }

    public function cadastrarProduto(Request $request){

        $dados = $request->all();

        $dados['user'] = \Auth::user()->id;

        $produto = Produto::create($dados);

        $foto = $request->file('foto_produto');

        if ($foto != null) {
            try{
                $nomeFoto = 'produto_' . $produto->id . '_.' . $foto->getClientOriginalExtension();

                Storage::delete('public/upload/produto/'.$nomeFoto);

                $foto->move(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO, $nomeFoto);

                $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $nomeFoto);

                $img->crop($dados['foto_w'], $dados['foto_h'], $dados['foto_x1'], $dados['foto_y1']);

                $img->resize(200, 200);

                Storage::delete('public/upload/produto/'.$nomeFoto);
                
                $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $nomeFoto);

                $produto->update([
                    'foto' => $nomeFoto
                ]);
            }
            catch(\Exception $e){
                //
            }
        }

        return redirect()->route('produtos');
    }

    public function editarProduto($id){

        $produto = Produto::find($id);
        $categorias = Categoria::all();

        return view('produtos::editar',[
            'produto'    => $produto,
            'categorias' => $categorias
        ]);

    }

    public function updateProduto(Request $request){

        $dados = $request->all();

        if($request->file('foto') == null){
            unset($dados['foto']);
        }

        $produto = Produto::find($dados['id']);
        $produto->update($dados);

        $foto = $request->file('foto_produto');

        if ($foto != null) {

            try{
                $nomeFoto = 'produto_' . $produto->id . '_.' . $foto->getClientOriginalExtension();

                Storage::delete('public/upload/produto/'.$nomeFoto);

                $foto->move(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO, $nomeFoto);

                $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $nomeFoto);

                $img->crop($dados['foto_w'], $dados['foto_h'], $dados['foto_x1'], $dados['foto_y1']);

                $img->resize(200, 200);

                Storage::delete('public/upload/produto/'.$nomeFoto);
                
                $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $nomeFoto);

                $produto->update([
                    'foto' => $nomeFoto
                ]);
            }
            catch(\Exception $e){
                //
            }

        }

        return redirect()->route('produtos');
    }

    public function deletarProduto($id){

        Produto::find($id)->delete();

        return redirect()->route('produtos');

    }

    public function dadosProduto(Request $request) {

        $dados = $request->all();

        $produtos = \DB::table('produtos as produto')
            ->leftJoin('categorias','produto.categoria','categorias.id')
            ->leftJoin('projetos_produtos','projetos_produtos.produto','produto.id');

        if(isset($dados['projeto'])){
            $produtos = $produtos->where('projetos_produtos.projeto','=', $dados['projeto']);
        }

        $produtos = $produtos->select([
                'produto.id',
                'produto.nome',
                'produto.descricao',
                'produto.disponivel',
                'produto.formato',
                'categorias.nome as categoria_nome',
                'produto.quantidade',
                'projetos_produtos.projeto',
        ]);

        return Datatables::of($produtos)
            ->addColumn('detalhes', function ($produto) use($dados) {

                $botoes =   "<span data-toggle='modal' data-target='#modal_detalhes'>
                                <a class='btn btn-outline btn-success detalhes_produto' data-placement='top' data-toggle='tooltip' title='Detalhes' produto='".$produto->id."'>
                                    <i class='icon wb-order' aria-hidden='true'></i>
                                </a>
                            </span>";
                if(!isset($dados['projeto'])){
                    $botoes .= "<span data-toggle='modal' data-target='#modal_editar'>
                        <a href='/produtos/editar/$produto->id' class='btn btn-outline btn-primary editar_produto' data-placement='top' data-toggle='tooltip' title='Editar' produto='".$produto->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>";
                }
                $botoes .= "<span data-toggle='modal' data-target='#modal_excluir'>
                                <a class='btn btn-outline btn-danger excluir_produto' data-placement='top' data-toggle='tooltip' title='Excluir' produto='".$produto->id."'>
                                    <i class='icon wb-trash' aria-hidden='true'></i>
                                </a>
                            </span>";
                return $botoes;
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function getDetalhesProduto(Request $request){

        $dados = $request->all();

        $produto = Produto::find($dados['id_produto']);

        $modalBody = '';

        $modalBody .= "<div class='col-xl-12 col-lg-12'>";
        $modalBody .= "<table class='table table-bordered table-hover table-striped'>";
        $modalBody .= "<thead>";
        $modalBody .= "</thead>";
        $modalBody .= "<tbody>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Nome:</b></td>";
        $modalBody .= "<td>".$produto->nome."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Descrição:</b></td>";
        $modalBody .= "<td>".$produto->descricao."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Status:</b></td>";
        if($produto->disponivel == 1)
            $modalBody .= "<td>Disponível</td>";
        else
            $modalBody .= "<td>Insdisponível</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Formato:</b></td>";
        if($produto->formato == 1)
            $modalBody .= "<td>Físico</td>";
        else
            $modalBody .= "<td>Digital</td>";
        $modalBody .= "</tr>";

        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Categoria:</b></td>";
        $modalBody .= "<td>".Categoria::find($produto->categoria)->nome."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Garantia:</b></td>";
        $modalBody .= "<td>".$produto->garantia."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Quantidade:</b></td>";
        $modalBody .= "<td>".$produto->quantidade."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Custo do produto:</b></td>";
        $modalBody .= "<td>".$produto->custo_produto."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Altura:</b></td>";
        $modalBody .= "<td>".$produto->altura."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Largura:</b></td>";
        $modalBody .= "<td>".$produto->largura."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Peso:</b></td>";
        $modalBody .= "<td>".$produto->peso."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "</thead>";
        $modalBody .= "</table>";
        $modalBody .= "</div>";
        $modalBody .= "<div class='text-center' style='margin-top: 20px'>";
        $modalBody .= "<img src='".'/'.CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO.$produto['foto']."' style='height: 200px'>";
        $modalBody .= "</div>";
        $modalBody .= "</div>";

        return response()->json($modalBody);
    }

    public function getProdutos(Request $request){

        $dados = $request->all();

        $produtos = Produto::select('nome','id')->where('user',\Auth::user()->id)->get()->toArray();

        $produtosDisponiveis = [];

        foreach($produtos as $produto){

            $projetoProduto = ProjetoProduto::where([
                ['produto',$produto['id']],
                ['projeto',$dados['projeto']]
            ])->first();

            if($projetoProduto != null)
                continue;

            $produtosDisponiveis[] = $produto;
        }

        return response()->json($produtosDisponiveis);
    }

    public function addProdutoProjeto(Request $request){

        $dados = $request->all();

        if(isset($dados['projeto']) && isset($dados['produto'])){

            ProjetoProduto::create([
                'projeto' => $dados['projeto'],
                'produto' => $dados['produto']
            ]);
            return response()->json('sucesso');
        }

        return response()->json('erro');
    }

    public function deletarProdutoPlano(Request $request){

        $dados = $request->all();

        if(isset($dados['projeto']) && isset($dados['produto'])){

            ProjetoProduto::where([
                'projeto' => $dados['projeto'],
                'produto' => $dados['produto']
            ])->first()->delete();
            return response()->json('sucesso');
        }

        return response()->json('erro');

    }

}


