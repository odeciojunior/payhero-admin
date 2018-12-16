<?php

namespace Modules\Produtos\Http\Controllers;

use App\Empresa;
use App\Produto;
use App\Categoria;
use App\ProjetoProduto;
use App\UsuarioEmpresa;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class ProdutosController extends Controller {


    public function index() {

        $empresas_usuario = UsuarioEmpresa::where('user',\Auth::user()->id)->pluck('empresa')->toArray();

        $produtos = Produto::whereIn('empresa',$empresas_usuario)->get()->toArray();

        return view('produtos::index',[
            'produtos' => $produtos
        ]);
    }

    public function cadastro() {

        $empresas = array();

        $empresas_usuario = UsuarioEmpresa::where('user', \Auth::user()->id)->get()->toArray();

        foreach($empresas_usuario as $empresa_usuario){
            $empresas[] = Empresa::find($empresa_usuario['empresa']);
        }

        $categorias = Categoria::all();

        return view('produtos::cadastro',[
            'categorias' => $categorias,
            'empresas' => $empresas
        ]);
    }

    public function cadastrarProduto(Request $request){

        $dados = $request->all();

        $produto = Produto::create($dados);

        $foto = $request->file('foto');

        if ($foto != null) {
            $nome_foto = 'produto_' . $produto->id . '_.' . $foto->getClientOriginalExtension();

            $foto->move(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO, $nome_foto);

            $produto->update([
                'foto' => $nome_foto
            ]);

        }

        return redirect()->route('produtos');
    }

    public function editarProduto($id){

        $produto = Produto::find($id);
        $categorias = Categoria::all();

        return view('produtos::editar',[
            'produto' => $produto,
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

        $foto = $request->file('foto');

        if ($foto != null) {
            $nome_foto = 'plano_' . $produto->id . '_.' . $foto->getClientOriginalExtension();

            $foto->move(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO, $nome_foto);

            $produto->update([
                'foto' => $nome_foto
            ]);

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

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Nome:</b></td>";
        $modal_body .= "<td>".$produto->nome."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Descrição:</b></td>";
        $modal_body .= "<td>".$produto->descricao."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Status:</b></td>";
        if($produto->disponivel == 1)
            $modal_body .= "<td>Disponível</td>";
        else
            $modal_body .= "<td>Insdisponível</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Formato:</b></td>";
        if($produto->formato == 1)
            $modal_body .= "<td>Físico</td>";
        else
            $modal_body .= "<td>Digital</td>";
        $modal_body .= "</tr>";

        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Categoria:</b></td>";
        $modal_body .= "<td>".Categoria::find($produto->categoria)->nome."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Garantia:</b></td>";
        $modal_body .= "<td>".$produto->garantia."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Quantidade:</b></td>";
        $modal_body .= "<td>".$produto->quantidade."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Custo do produto:</b></td>";
        $modal_body .= "<td>".$produto->custo_produto."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Altura:</b></td>";
        $modal_body .= "<td>".$produto->altura."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Largura:</b></td>";
        $modal_body .= "<td>".$produto->largura."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Peso:</b></td>";
        $modal_body .= "<td>".$produto->peso."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "</thead>";
        $modal_body .= "</table>";
        $modal_body .= "</div>";
        $modal_body .= "<div class='text-center' style='margin-top: 20px'>";
        $modal_body .= "<img src='".'/'.CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO.$produto['foto']."' style='height: 200px'>";
        $modal_body .= "</div>";
        $modal_body .= "</div>";

        return response()->json($modal_body);
    }

    public function getProdutos(Request $request){

        $dados = $request->all();

        $empresas = array();

        $empresas_usuario = UsuarioEmpresa::where('user',\Auth::user()->id)->pluck('empresa')->toArray();

        $produtos = Produto::select('nome','id')->where('empresa',$empresas_usuario)->get()->toArray();

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
