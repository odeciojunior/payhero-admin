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

        $modalBody = '';

        $modalBody .= "<div class='col-xl-12 col-lg-12'>";
        $modalBody .= "<table class='table table-bordered table-hover table-striped'>";
        $modalBody .= "<thead>";
        $modalBody .= "</thead>";
        $modalBody .= "<tbody>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Nome:</b></td>";
        $modalBody .= "<td>".$categoria->nome."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Descrição:</b></td>";
        $modalBody .= "<td>".$categoria->descricao."</td>";
        $modalBody .= "</tr>";
        foreach($produtos as $produto){
            $modalBody .= "<tr>";
            $modalBody .= "<td><b>Produto:</b></td>";
            $modalBody .= "<td>".$produto['nome']."</td>";
            $modalBody .= "</tr>";
        }
        $modalBody .= "</thead>";
        $modalBody .= "</table>";
        $modalBody .= "</div>";
        $modalBody .= "</div>";

        return response()->json($modalBody);
    }
}
