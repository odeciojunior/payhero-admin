<?php

namespace Modules\Parceiros\Http\Controllers;

use App\User;
use App\UserProjeto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class ParceirosController extends Controller {


    public function dadosParceiros(Request $request) {

        $dados = $request->all();

        $projetos = \DB::table('projetos as projeto')
            ->leftJoin('users_projetos as user_projeto','projeto.id','user_projeto.projeto')
            ->leftJoin('users as user','user_projeto.user','user.id')
            ->where('projeto.id',$dados['projeto'])
            ->get([
                'projeto.id',
                'user.name',
                'user_projeto.tipo',
                'user_projeto.status'
        ]);

        return Datatables::of($projetos)
        ->addColumn('detalhes', function ($projeto) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_parceiro' data-placement='top' data-toggle='tooltip' title='selecionar'>
                            <i class='icon wb-menu' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a class='btn btn-outline btn-primary editar_parceiro' data-placement='top' data-toggle='tooltip' title='Editar' projeto='".$projeto->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_parceiro' data-placement='top' data-toggle='tooltip' title='Excluir' projeto='".$projeto->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function cadastrarParceiro(Request $request){

        $dados = $request->all();

        $user = User::find($dados['email_parceiro']);

        if($user != null){
            $dados['user'] = $user['id'];
            $dados['status'] = 'ativo';
        }
        else{
            $dados['status'] = 'convite enviado';
        }

        UserProjeto::create($dados);

        return response()->json('sucesso');
    }

    public function getFormAddParceiro(){

        $form = view('parceiros::cadastro');

        return response()->json($form->render());
    }


}
