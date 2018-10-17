<?php

namespace Modules\Empresas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\User;
use App\Empresa;
use Auth;

class EmpresasController extends Controller {

    public function index() {

        return view('empresas::index'); 
    }

    public function cadastro() {

        return view('empresas::cadastro');
    }

    public function cadastrarEmpresa(Request $request){

        $dados = $request->all();

        Empresa::create($dados);

        return redirect()->route('empresas');
    }

    public function editarEmpresa($id){

        $empresa = Empresa::find($id);

        return view('empresas::editar',[
            'empresa' => $empresa,
        ]);

    }

    public function updateEmpresa(Request $request){

        $dados = $request->all();

        Empresa::find($dados['id'])->update($dados);

        return view('empresas::index');
    }

    public function deletarEmpresa($id){

        Empresa::find($id)->delete();

        return redirect()->route('empresas');

    }

    public function dadosEmpresas() {

        $empresas = \DB::table('empresas as empresa')
            ->get([
                'id',
                'cnpj',
                'nome',
                'email',
                'municipio',
                'uf',
                'situacao',
        ]);

        return Datatables::of($empresas)
        ->addColumn('detalhes', function ($empresa) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_empresa' data-placement='top' data-toggle='tooltip' title='Detalhes' empresa='".$empresa->id."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a href='/empresas/editar/$empresa->id' class='btn btn-outline btn-primary editar_empresa' data-placement='top' data-toggle='tooltip' title='Editar' empresa='".$empresa->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_empresa' data-placement='top' data-toggle='tooltip' title='Excluir' empresa='".$empresa->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function getDetalhesEmpresa(Request $request){

        $dados = $request->all();

        $empresa = Empresa::find($dados['id_empresa']);

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Status:</b></td>";
        $modal_body .= "<td>".$empresa->status."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>CNPJ:</b></td>";
        $modal_body .= "<td>".$empresa->cnpj."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Nome fantasia:</b></td>";
        $modal_body .= "<td>".$empresa->nome."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Email:</b></td>";
        $modal_body .= "<td>".$empresa->email."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Telefone:</b></td>";
        $modal_body .= "<td>".$empresa->telefone."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>CEP:</b></td>";
        $modal_body .= "<td>".$empresa->cep."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Estado:</b></td>";
        $modal_body .= "<td>".$empresa->estado."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Cidade:</b></td>";
        $modal_body .= "<td>".$empresa->cidade."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Bairro:</b></td>";
        $modal_body .= "<td>".$empresa->bairro."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Rua:</b></td>";
        $modal_body .= "<td>".$empresa->logradouro."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Número:</b></td>";
        $modal_body .= "<td>".$empresa->numero."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Complemento:</b></td>";
        $modal_body .= "<td>".$empresa->complemento."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Atividade principal:</b></td>";
        $modal_body .= "<td>".$empresa->atividade_principal."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Capital social:</b></td>";
        $modal_body .= "<td>".$empresa->capital_social."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Data de abertura:</b></td>";
        $modal_body .= "<td>".$empresa->abertura."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "</thead>";
        $modal_body .= "</table>";
        $modal_body .= "</div>";
        $modal_body .= "</div>";

        return response()->json($modal_body);
    }
}


