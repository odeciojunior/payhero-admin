<?php

namespace Modules\Afiliados\Http\Controllers;

use App\Afiliado;
use App\UserProjeto;
use Illuminate\Http\Request;
use App\SolicitacaoAfiliacao;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
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


}
