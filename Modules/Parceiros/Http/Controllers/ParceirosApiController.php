<?php

namespace Modules\Parceiros\Http\Controllers;

use App\User;
use App\Convite;
use App\Empresa;
use App\Parceiro;
use Carbon\Carbon;
use App\UserProjeto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\Parceiros\Transformers\ParceirosResource;

class ParceirosApiController extends Controller {

    public function index(Request $request) {

        $parceiros = UserProjeto::where([
            ['tipo', '!=', 'produtor'],
            ['projeto', $request->id_projeto]
        ]);

        return ParceirosResource::collection($parceiros->paginate());
    }

    public function store(Request $request) {

        $dados = $request->all();
        $dados['projeto'] = $request->id_projeto;

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

        $parceiro = UserProjeto::select('user','tipo','status','valor_remuneracao','created_at')->where('id',$request->id_parceiro)->first();
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

        $dados = $request->all();

        UserProjeto::find($dados['id'])->update($dados);

        return response()->json('sucesso');
    }

    public function destroy(Request $request) {

        UserProjeto::find($request->id_parceiro)->delete();

        return response()->json('sucesso');
    }

}
