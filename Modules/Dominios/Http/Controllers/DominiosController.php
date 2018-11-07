<?php

namespace Modules\Dominios\Http\Controllers;

use App\Layout;
use App\Dominio;
use App\Empresa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class DominiosController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index() {

        return view('dominios::index'); 
    }

    /**
     * Display a form to store new users.
     * @return Response
     */
    public function cadastro() {

        $layouts = Layout::all();
        $empresas = Empresa::all();

        return view('dominios::cadastro',[
            'layouts' => $layouts,
            'empresas' => $empresas
        ]);
    }

    public function cadastrarDominio(Request $request){

        $dados = $request->all();

        Dominio::create($dados);

        return redirect()->route('dominios');
    }

    public function editarDominio($id){

        $dominio = Dominio::find($id);
        $layouts = Layout::all();
        $empresas = Empresa::all();

        return view('dominios::editar',[
            'dominio' => $dominio,
            'layouts' => $layouts,
            'empresas' => $empresas
        ]);

    }

    public function updateDominio(Request $request){

        $dados = $request->all();

        Dominio::find($dados['id'])->update($dados);

        return redirect()->route('dominios');
    }

    public function deletarDominio($id){

        Dominio::find($id)->delete();

        return redirect()->route('dominios');

    }

    /**
     * Return data for datatable
     */
    public function dadosDominios() {

        $dominios = \DB::table('dominios as dominio')
            ->leftJoin('layouts', 'dominio.layout', 'layouts.id')
            ->leftJoin('empresas', 'dominio.empresa', 'empresas.id')
            ->get([
                'dominio.id',
                'dominio.dominio',
                'dominio.layout',
                'dominio.empresa',
                'empresas.nome as empresa_nome',
                'layouts.descricao as layout_descricao',
        ]);

        return Datatables::of($dominios)
            ->addColumn('detalhes', function ($dominio) {
            return "<span data-toggle='modal' data-target='#modal_editar'>
                        <a href='/dominios/editar/$dominio->id' class='btn btn-outline btn-primary editar_dominio' data-placement='top' data-toggle='tooltip' title='Editar' dominio='".$dominio->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_dominio' data-placement='top' data-toggle='tooltip' title='Excluir' dominio='".$dominio->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

}
