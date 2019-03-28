<?php

namespace Modules\Afiliados\Http\Controllers;

use App\Projeto;
use App\Empresa;
use App\Afiliado;
use App\UserProjeto;
use Illuminate\Http\Request;
use App\SolicitacaoAfiliacao;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Afiliados\Transformers\MeusAfiliadosResource;
use Modules\Afiliados\Transformers\MinhasAfiliacoesResource;
use Modules\Afiliados\Transformers\MeusAfiliadosSolicitacoesResource;
use Modules\Afiliados\Transformers\MinhasAfiliacoesSolicitacoesResource;

class AfiliadosApiController extends Controller {


    public function meusAfiliados() {

        $projetos_usuario = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor']
        ])->pluck('projeto')->toArray();

        $afiliados = Afiliado::whereIn('projeto',$projetos_usuario)->whereNull('deleted_at');

        return MeusAfiliadosResource::collection($afiliados->paginate());

    }

    public function meusAfiliadosSolicitacoes() {

        $projetos_usuario = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor']   
        ])->pluck('projeto')->toArray();

        $solicitacoes_afiliacoes = SolicitacaoAfiliacao::whereIn('projeto',$projetos_usuario)
            ->whereNull('deleted_at')
            ->where('status','Pendente');

        return MeusAfiliadosSolicitacoesResource::collection($solicitacoes_afiliacoes->paginate());

    }

    public function minhasAfiliacoes() {

        $afiliacoes = Afiliado::where('user',\Auth::user()->id);

        return MinhasAfiliacoesResource::collection($afiliacoes->paginate());

    }

    public function minhasAfiliacoesSolicitacoes() {

        $solicitacoes_afiliacoes = SolicitacaoAfiliacao::whereNull('deleted_at')
            ->where('user',\Auth::user()->id)
            ->whereIn('status',['Pendente','Negada']);

        return MinhasAfiliacoesSolicitacoesResource::collection($solicitacoes_afiliacoes->paginate());

    }

    public function store(Request $request){

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto['afiliacao_automatica']){
 
            SolicitacaoAfiliacao::create([
                'user'      => \Auth::user()->id,
                'projeto'   => Hashids::decode($projeto['id']),
                'status'    => 'Pendente'
            ]);

            return response()->json('pendente');
        }

        $empresa = Empresa::where([
            ['user', \Auth::user()->id],
            ['recipient_id','!=','']
        ])->first();

        $afiliado = Afiliado::create([
            'user' => \Auth::user()->id,
            'projeto' => $projeto['id'],
            'porcentagem' => $projeto['porcentagem_afiliados'],
            'empresa'  => @$empresa->id
        ]);

        return response()->json('sucesso');

    }

    public function destroy(){

        Afiliado::find(Hashids::decode($request->id_afiliado))->delete();

        return response()->json('sucesso');
    }

    public function destroySolicitacao(){

        SolicitacaoAfiliacao::find(Hashids::decode($request->id_solicitacao))->delete();

        return response()->json('sucesso');
    }


}
