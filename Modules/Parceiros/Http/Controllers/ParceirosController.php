<?php

namespace Modules\Parceiros\Http\Controllers;

use App\User;
use App\Convite;
use App\Empresa;
use App\UserProjeto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class ParceirosController extends Controller {

    public function dadosParceiros(Request $request) {

        $dados = $request->all();

        $parceiros = \DB::table('projetos as projeto')
            ->leftJoin('users_projetos as user_projeto','projeto.id','user_projeto.projeto')
            ->leftJoin('users as user','user_projeto.user','user.id')
            ->where('tipo','!=','produtor')
            // ->where('user_projeto.user','<',\Auth::user()->id)
            // ->orWhereNull('user_projeto.user')
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

        $dados = $request->all();

        $user = User::where('email',$dados['email_parceiro'])->first();

        if($user != null){
            $dados['user'] = $user['id'];
            $dados['status'] = 'ativo';
            $empresas = Empresa::where('user',$user['id'])->get()->toArray();

            if(count($empresas) > 0){
                foreach($empresas as $empresa){
                    if($empresa['recipient_id'] != ''){
                        $dados['empresa'] = $empresa['id'];
                        break;
                    }
                }
            }
        }
        else{
            $dados['status'] = 'convite enviado';

            $dados_convite['email_convidado'] = $dados['email_parceiro'];
            $dados_convite['user_convite'] = \Auth::user()->id;
            $dados_convite['status'] = "Convite enviado";
            $dados_convite['parametro']  = $this->randString(15);

            $dados_convite['empresa'] = @Empresa::where('user', \Auth::user()->id)->first()->id;

            $convite = Convite::create($dados_convite);

            Mail::send('convites::email_convite', [ 'convite' => $convite ], function ($mail) use ($dados_convite) {
                $mail->from('julioleichtweis@gmail.com', 'Cloudfox');

                $mail->to($dados_convite['email_convidado'], 'Cloudfox')->subject('Convite para participar de um projeto no Cloudfox!');
            });
    

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

    function randString($size){

        $novo_parametro = false;

        while(!$novo_parametro){

            $basic = 'abcdefghijlmnopqrstuvwxyz0123456789';

            $parametro = "";

            for($count= 0; $size > $count; $count++){
                $parametro.= $basic[rand(0, strlen($basic) - 1)];
            }

            $convite = Convite::where('parametro', $parametro)->first();

            if($convite == null){
                $novo_parametro = true;
            }

        }

        return $parametro;
    }

}
