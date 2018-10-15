<?php

namespace Modules\Empresas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\User;
use App\ModelHasRoles;
use App\Role;
use Auth;

class EmpresasController extends Controller {

    public function index() {

        return view('empresas::index'); 
    }

    public function cadastro() {

        return view('empresas::cadastro');
    }

    public function cadastrarempresas(Request $request){

        $dados = $request->all();

        $dados['password'] = bcrypt($dados['password']);

        $user = User::create($dados);

        $user->assignRole('administrador geral');

        return view('empresas::index');
    }

    public function editarEmpresas($id){

        $user = User::find($id);

        if(auth()->user()->hasRole('administrador geral')){
            $roles = Role::all();
        }
        else{
            $roles = Role::where('name', '!=' , 'administrador geral')->get()->toArray();
        }

        return view('empresas::editar',[
            'user' => $user,
            'roles' => $roles
        ]);

    }

    public function updateEmpresas(Request $request){

        $dados = $request->all();

        User::find($dados['id'])->update($dados);

        return view('empresas::index');
    }

    public function deletarempresas($id){

        User::find($id)->delete();

        return view('empresas::index');

    }

    public function dadosEmpresas() {

        $empresas = \DB::table('empresas as empresa')
            ->get([
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
                        <a href='/empresass/editar/$empresa->id' class='btn btn-outline btn-primary editar_empresa' data-placement='top' data-toggle='tooltip' title='Editar' empresa='".$empresa->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_user' data-placement='top' data-toggle='tooltip' title='Excluir' empresa='".$empresa->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }


    public function getDetalhesempresas(Request $request){

        $dados = $request->all();

        $user = User::find($dados['id_user']);
        $role_user = ModelHasRoles::where('model_id',$user->id)->first();
        $role = Role::find($role_user->role_id);

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Função:</b></td>";
        $modal_body .= "<td>".$role->name."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Nome:</b></td>";
        $modal_body .= "<td>".$user->name."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Email:</b></td>";
        $modal_body .= "<td>".$user->email."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Data de nascimento:</b></td>";
        $modal_body .= "<td>".$user->data_nascimento."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Celular:</b></td>";
        $modal_body .= "<td>".$user->celular."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Telefone 1:</b></td>";
        $modal_body .= "<td>".$user->telefone1."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Telefone 2:</b></td>";
        $modal_body .= "<td>".$user->telefone2."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>CPF:</b></td>";
        $modal_body .= "<td>".$user->cpf."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>CEP:</b></td>";
        $modal_body .= "<td>".$user->cep."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>País:</b></td>";
        $modal_body .= "<td>".$user->pais."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Estado:</b></td>";
        $modal_body .= "<td>".$user->estado."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Cidade:</b></td>";
        $modal_body .= "<td>".$user->cidade."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Bairro:</b></td>";
        $modal_body .= "<td>".$user->bairro."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Rua:</b></td>";
        $modal_body .= "<td>".$user->logradouro."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Número:</b></td>";
        $modal_body .= "<td>".$user->numero."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Complemento:</b></td>";
        $modal_body .= "<td>".$user->complemento."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Referência:</b></td>";
        $modal_body .= "<td>".$user->referencia."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "</thead>";
        $modal_body .= "</table>";
        $modal_body .= "</div>";
        $modal_body .= "</div>";

        return response()->json($modal_body);
    }
}


