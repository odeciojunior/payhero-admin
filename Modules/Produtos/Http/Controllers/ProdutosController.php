<?php

namespace Modules\Produtos\Http\Controllers;

use App\Produto;
use App\Categoria;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class ProdutosController extends Controller
{
    public function index() {

        return view('produtos::index'); 
    }

    public function cadastro() {

        $categorias = Categoria::all();

        return view('produtos::cadastro',
        [
            'categorias' => $categorias
        ]);
    }

    public function cadastrarProduto(Request $request){

        $dados = $request->all();

        $produto = Produto::create($dados);

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

    public function dadosProduto() {

        $produtos = \DB::table('produtos as produto')
            ->leftJoin('categorias','produto.categoria','categorias.id')
            ->get([
                'produto.id',
                'produto.nome',
                'produto.descricao',
                'produto.disponivel',
                'produto.formato',
                'categorias.nome as categoria_nome',
                'produto.quntidade',
        ]);

        return Datatables::of($produtos)
        ->addColumn('detalhes', function ($produto) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_produto' data-placement='top' data-toggle='tooltip' title='Detalhes' produto='".$produto->id."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a href='/produtos/editar/$produto->id' class='btn btn-outline btn-primary editar_produto' data-placement='top' data-toggle='tooltip' title='Editar' produto='".$produto->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_produto' data-placement='top' data-toggle='tooltip' title='Excluir' produto='".$produto->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
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
        $modal_body .= "<td><b>Categoria:</b></td>";
        $modal_body .= "<td>".Categoria::find($produto->categoria)->nome."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Garantia:</b></td>";
        $modal_body .= "<td>".$produto->garantia."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Quantidade:</b></td>";
        $modal_body .= "<td>".$produto->quntidade."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Custo do produto:</b></td>";
        $modal_body .= "<td>".$produto->custo_produto."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "</thead>";
        $modal_body .= "</table>";
        $modal_body .= "</div>";

        $modal_body .= "<img src='".url(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO.$produto->foto)."'>";// alt='Foto não encontrada.'>";
        $modal_body .= "</div>";

        return response()->json($modal_body);
    }


}
