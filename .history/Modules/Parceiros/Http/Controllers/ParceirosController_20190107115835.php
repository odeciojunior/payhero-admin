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

        $parceiros = \DB::table('projetos as projeto')
            ->leftJoin('users_projetos as user_projeto','projeto.id','user_projeto.projeto')
            ->leftJoin('users as user','user_projeto.user','user.id')
            ->where('user_projeto.user','>',\Auth::user()->id)
            ->where('user_projeto.user','<',\Auth::user()->id)
            ->orWhereNull('user_projeto.user')
            ->where('projeto.id',$dados['projeto'])
            ->get([
                'user_projeto.id',
                'user.name',
                'user_projeto.tipo',
                'user_projeto.status'
        ]);

        return Datatables::of($parceiros)
        ->addColumn('detalhes', function ($parceiro) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_parceiro' data-placement='top' data-toggle='tooltip' title='Detalhes' parceiro='".$parceiro->id."'>
                            <i class='icon wb-menu' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a class='btn btn-outline btn-primary editar_parceiro' data-placement='top' data-toggle='tooltip' title='Editar' parceiro='".$parceiro->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_parceiro' data-placement='top' data-toggle='tooltip' title='Excluir' parceiro='".$parceiro->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function cadastrarParceiro(Request $request){

        $dados = $request->all();        return response()->json('lala');


        $user = User::where('email',$dados['email_parceiro'])->first();

        if($user != null){
            $dados['user'] = $user['id'];
            $dados['status'] = 'ativo';
        }
        else{
            $dados['status'] = 'convite enviado';
        }

        if(isset($dados['responsavel_frete']) && $dados['responsavel_frete'] == 'on'){
            $dados['responsavel_frete'] = true;
        }

        UserProjeto::create($dados);

        return response()->json('sucesso');
    }

    public function editarParceiro(Request $request){

        $dados = $request->all();

        UserProjeto::find($dados['id'])->update($dados);

        return response()->json('sucesso');
    }

    public function removerParceiro(Request $request){

        $dados = $request->all();

        UserProjeto::find($dados['id'])->delete();

        return response()->json('sucesso');
    }

    public function getFormAddParceiro(){

        $form = view('parceiros::cadastro');

        return response()->json($form->render());
    }

    public function getFormEditarParceiro(Request $request){

        $dados = $request->all();

        $parceiro = UserProjeto::find($dados['id_parceiro']);

        $user = User::find($parceiro['user']);

        $form = view('parceiros::editar',[
            'parceiro' => $parceiro,
            'user' => $user
        ]);

        return response()->json($form->render());
    }

    public function detalhesParceiro(Request $request){

        $dados = $request->all();

        $parceiro = UserProjeto::find($dados['parceiro']);

        $user = User::find($parceiro['user']);

        $detalhes = view('parceiros::detalhesparceiro',[
            'parceiro' => $parceiro,
            'user'     => $user,
        ]);

        return response()->json($detalhes->render());
    }

}
