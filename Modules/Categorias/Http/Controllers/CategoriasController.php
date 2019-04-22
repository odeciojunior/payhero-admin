<?php

namespace Modules\Categorias\Http\Controllers;

use App\Produto;
use App\Categoria;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class CategoriasController extends Controller {

    public function index() {

        return view('categorias::index'); 
    }

    public function cadastro() {

        return view('categorias::cadastro');
    }

    public function cadastrarCategoria(Request $request){

        $dados = $request->all();

        Categoria::create($dados);

        return redirect()->route('categorias');
    }

    public function editarCategoria($id){

        $categoria = Categoria::find($id);

        return view('categorias::editar',[
            'categoria' => $categoria,
        ]);

    }

    public function updateCategoria(Request $request){

        $dados = $request->all();

        Categoria::find($dados['id'])->update($dados);

        return redirect()->route('categorias');
    }

    public function deletarCategoria($id){

        Categoria::find($id)->delete();

        return redirect()->route('categorias');

    }

    public function dadosCategoria() {

        $categorias = \DB::table('categorias as categoria')
            ->get([
                'id',
                'nome',
                'descricao',
        ]);

        return Datatables::of($categorias)
        ->addColumn('detalhes', function ($categoria) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_categoria' data-placement='top' data-toggle='tooltip' title='Detalhes' categoria='".$categoria->id."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a href='/categorias/editar/$categoria->id' class='btn btn-outline btn-primary editar_categoria' data-placement='top' data-toggle='tooltip' title='Editar' categoria='".$categoria->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_categoria' data-placement='top' data-toggle='tooltip' title='Excluir' categoria='".$categoria->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function getDetalhesCategoria(Request $request){

        $dados = $request->all();

        $categoria = Categoria::find($dados['id_categoria']);

        $produtos = Produto::where('categoria', $categoria->id)->get()->toArray();

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Nome:</b></td>";
        $modal_body .= "<td>".$categoria->nome."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Descrição:</b></td>";
        $modal_body .= "<td>".$categoria->descricao."</td>";
        $modal_body .= "</tr>";
        foreach($produtos as $produto){
            $modal_body .= "<tr>";
            $modal_body .= "<td><b>Produto:</b></td>";
            $modal_body .= "<td>".$produto['nome']."</td>";
            $modal_body .= "</tr>";
        }
        $modal_body .= "</thead>";
        $modal_body .= "</table>";
        $modal_body .= "</div>";
        $modal_body .= "</div>";

        return response()->json($modal_body);
    }
}
