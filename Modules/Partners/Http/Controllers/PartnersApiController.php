<?php

namespace Modules\Partners\Http\Controllers;

use App\Entities\User;
use App\Projeto;
use App\Convite;
use App\Empresa;
use App\Parceiro;
use Carbon\Carbon;
use App\Entities\UserProjeto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Mail;
use Modules\Partners\Transformers\PartnersResource;

class PartnersApiController extends Controller {

    public function index(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $parceiros = UserProjeto::where([
            ['tipo', '!=', 'produtor'],
            ['projeto', Hashids::decode($request->id_projeto)]
        ]);

        return ParceirosResource::collection($parceiros->paginate());
    }

    public function store(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $dados = $request->all();
        $dados['projeto'] = $projeto['id'];

        $user = User::where('email',$dados['email'])->first();

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

            $dados_convite['email_convidado'] = $dados['email'];
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

    public function show(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $parceiro = UserProjeto::select('user','tipo','status','valor_remuneracao','created_at')
                                ->where('id',Hashids::decode($request->id_parceiro))->first();

        $user = User::find($parceiro->user);
        
        return response()->json([
            'nome' => $user['name'],
            'tipo' => $parceiro->tipo,
            'status' => $parceiro->status,
            'valor_remuneracao' => $parceiro->valor_remuneracao,
            'created_at' => with(new Carbon($parceiro->created_at))->format('d/m/Y H:i:s')
        ]);
    }

    public function update(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $dados = $request->all();

        UserProjeto::find(Hashids::decode($dados['id']))->update($dados);

        return response()->json('sucesso');
    }

    public function destroy(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        UserProjeto::find(Hashids::decode($request->id_parceiro))->delete();

        return response()->json('sucesso');
    }

    public function isAuthorized($id_projeto){

        $projeto_usuario = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor'],
            ['projeto', $id_projeto]
        ])->first();

        if(!$projeto_usuario){
            return false;
        }

        return true;
    }

}
