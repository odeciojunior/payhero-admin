<?php

namespace Modules\Afiliados\Http\Controllers;

use App\Foto;
use App\User;
use App\Plano;
use App\Dominio;
use App\Empresa;
use App\Projeto;
use App\Afiliado;
use Carbon\Carbon;
use App\UserProjeto;
use App\LinkAfiliado;
use App\Notification;
use App\MaterialExtra;
use Illuminate\Http\Request;
use App\SolicitacaoAfiliacao;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\DataTables\Facades\DataTables;
use Modules\Notificacoes\Notifications\NovaAfiliacao;
use Modules\Notificacoes\Notifications\AfiliacaoAprovada;
use Modules\Notificacoes\Notifications\NovaSolicitacaoAfiliacao;

class AfiliadosController extends Controller {

    public function afiliar($id_projeto) {

        date_default_timezone_set('America/Sao_Paulo');

        $projeto = Projeto::where('id',Hashids::decode($id_projeto))->first();

        $userProjeto = UserProjeto::where([
            ['projeto', $projeto['id']],
            ['tipo', 'produtor']
        ])->first();

        $user = User::find($userProjeto['user']);

        if(!$projeto['afiliacao_automatica']){

            $notificacao = Notification::where([
                ['notifiable_id',$user['id']],
                ['type','Modules\Notificacoes\Notifications\NovaSolicitacaoAfiliacao']
            ])
            ->whereNull('read_at')
            ->first();

            if($notificacao){
                $data = json_decode($notificacao['data']);
                $notificacao->update([
                    'data' => json_encode(['qtd' => preg_replace("/[^0-9]/", "", $data->qtd) + 1 ])
                ]);
            }
            else{
                $user->notify(new NovaSolicitacaoAfiliacao());
            }

            SolicitacaoAfiliacao::create([
                'user'      => \Auth::user()->id,
                'projeto'   => $projeto['id'],
                'status'    => 'Pendente'
            ]);

            \Session::flash('success', "Solicitação de afiliação enviada para o produtor do projeto!");
            return redirect()->route('afiliados.minhasafiliacoes');
        }

        $empresa = Empresa::where('user', \Auth::user()->id)->first();

        $afiliado = Afiliado::create([
            'user' => \Auth::user()->id,
            'projeto' => $projeto['id'],
            'porcentagem' => $projeto['porcentagem_afiliados'],
            'empresa'  => @$empresa->id
        ]);

        $notificacao = Notification::where([
            ['notifiable_id',$user['id']],
            ['type','Modules\Notificacoes\Notifications\NovaAfiliacao']
        ])
        ->whereNull('read_at')
        ->first();

        if($notificacao){
            $data = json_decode($notificacao['data']);
            $notificacao->update([
                'data' => json_encode(['qtd' => preg_replace("/[^0-9]/", "", $data->qtd) + 1 ])
            ]);
        }
        else{
            $user->notify(new NovaAfiliacao());
        }

        return redirect()->route('afiliados.minhasafiliacoes');
    }

    public function confirmarAfiliacao(Request $request) {

        $dados = $request->all();

        $solicitacao_afiliacao = SolicitacaoAfiliacao::where('id',Hashids::decode($dados['id']))->first();

        $projeto = Projeto::where('id',$solicitacao_afiliacao['projeto'])->first();

        $user = User::find($solicitacao_afiliacao['user']);

        $empresa = Empresa::where('user', $user['id'])->first();

        $afiliado = Afiliado::create([
            'user' => $user['id'],
            'projeto' => $projeto['id'],
            'porcentagem' => $projeto['porcentagem_afiliados'],
            'empresa'  => @$empresa->id
        ]);

        $solicitacao_afiliacao->update([
            'status' => 'Confirmada'
        ]);

        $notificacao = Notification::where([
            ['notifiable_id',$user['id']],
            ['type','Modules\Notificacoes\Notifications\AfiliacaoAprovada']
        ])
        ->whereNull('read_at')
        ->first();

        if($notificacao){
            $data = json_decode($notificacao['data']);
            $notificacao->update([
                'data' => json_encode(['qtd' => preg_replace("/[^0-9]/", "", $data->qtd) + 1 ])
            ]);
        }
        else{
            $user->notify(new AfiliacaoAprovada());
        }

        return response()->json('Sucesso');
    } 

    public function excluirAfiliacao(Request $request){

        $dados = $request->all();

        $afiliado = Afiliado::where('id',Hashids::decode($dados['afiliado']))->first();
        $afiliado->delete();

        return response()->json('sucesso');
    }

    public function meusAfiliados(){

        return view('afiliados::meus_afiliados');
    }

    public function minhasAfiliacoes(){
 
        $afiliacoes = Afiliado::where('user',\Auth::user()->id)->get()->toArray();

        $projetos = [];

        if(count($afiliacoes) > 0){
            foreach($afiliacoes as $afiliacao){
                $projeto = Projeto::find($afiliacao['projeto']);
                $p['id_afiliacao'] = Hashids::encode($afiliacao['id']);
                $p['foto'] = $projeto['foto'];
                $p['nome'] = $projeto['nome'];
                $p['descricao'] = $projeto['descricao'];
                $projetos[] = $p;

            }
        }

        return view('afiliados::minhas_afiliacoes',[
            'projetos' => $projetos
        ]);
    }

    public function afiliacao($id_afiliacao){

        $afiliado = Afiliado::where('id',Hashids::decode($id_afiliacao))->first();
        $id_afiliado = Hashids::encode($afiliado->id);

        $projeto = Projeto::find($afiliado['projeto']);

        $empresas = Empresa::where('user',\Auth::user()->id)->get()->toArray();

        $projeto_usuario = UserProjeto::where([
            ['projeto',$projeto['id']],
            ['tipo','produtor']
        ])->first();
        $usuario = User::find($projeto_usuario['user']);

        $materiais_extras = MaterialExtra::where('projeto',$projeto['id'])->get()->toArray();

        return view('afiliados::detalhes_afiliacao',[
            'id_afiliado' => $id_afiliado,
            'projeto' => $projeto,
            'produtor' => $usuario['name'],
            'empresas' => $empresas,
            'afiliado' => $afiliado,
            'materiais_extras' => $materiais_extras,
        ]);
    }

    public function dadosMeusAfiliados(){

        $projetos_usuario = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor']
        ])->pluck('projeto')->toArray();

        $usuarios_afiliados = \DB::table('afiliados as afiliado')
            ->leftJoin('users as user','afiliado.user','user.id')
            ->leftJoin('projetos as projeto','afiliado.projeto','projeto.id')
            ->whereIn('projeto.id',$projetos_usuario)
            ->whereNull('afiliado.deleted_at')
            ->select([
                'afiliado.id',
                'afiliado.deleted_at',
                'user.name',
                'afiliado.porcentagem',
                'projeto.nome',
        ]);

        return Datatables::of($usuarios_afiliados)
        ->addColumn('detalhes', function ($afiliado) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_afiliado' data-placement='top' data-toggle='tooltip' title='Detalhes' afiliado='".Hashids::encode($afiliado->id)."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                            Detalhes
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_remover_afiliado' style='margin-left:10px'>
                        <a class='remover_afiliado btn btn-outline btn-danger' data-placement='top' data-toggle='tooltip' title='Remover afiliado' afiliado='".Hashids::encode($afiliado->id)."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                            Remover afiliado
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);

    }

    public function dadosAfiliacoesPendentes(){

        $projetos_usuario = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor']   
        ])->pluck('projeto')->toArray();

        $solicitacoes_afiliacoes = \DB::table('solicitacoes_afiliacoes as solicitacao_afiliacao')
            ->leftJoin('users as user','solicitacao_afiliacao.user','user.id')
            ->leftJoin('projetos as projeto','solicitacao_afiliacao.projeto','projeto.id')
            ->whereIn('solicitacao_afiliacao.projeto',$projetos_usuario)
            ->whereNull('solicitacao_afiliacao.deleted_at')
            ->where('solicitacao_afiliacao.status','Pendente')
            ->select([
                'solicitacao_afiliacao.id',
                'user.name',
                'projeto.porcentagem_afiliados',
                'solicitacao_afiliacao.created_at as data_solicitacao',
                'projeto.nome',
        ]);

        return Datatables::of($solicitacoes_afiliacoes)
        ->editColumn('data_solicitacao', function($afiliado){
            return Carbon::parse($afiliado->data_solicitacao)->format('d/m/Y H:i');
        })
        ->addColumn('detalhes', function ($solicitacao_afiliacao) {
            return "<span>
                        <a class='btn btn-outline btn-success confirmar_afiliacao' data-placement='top' data-toggle='tooltip' title='Confirmar' solicitacao_afiliacao='".Hashids::encode($solicitacao_afiliacao->id)."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                            Confirmar afiliação
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_cancelar_solicitacao'>
                        <a class='cancelar_solicitacao btn btn-outline btn-danger' data-placement='top' data-toggle='tooltip' title='cancelar solicitação' solicitacao_afiliacao='".Hashids::encode($solicitacao_afiliacao->id)."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                            Negar solicitação
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);

    }

    public function dadosMinhasAfiliacoesPendentes(){

        $empresas_usuario = Empresa::where('user',\Auth::user()->id)->pluck('id')->toArray();

        $projetos_usuario = UserProjeto::where('user',\Auth::user()->id)->pluck('id')->toArray();

        $solicitacoes_afiliacoes = \DB::table('solicitacoes_afiliacoes as solicitacao_afiliacao')
            ->leftJoin('projetos as projeto','projeto.id','=','solicitacao_afiliacao.projeto')
            ->whereNull('solicitacao_afiliacao.deleted_at')
            ->where('solicitacao_afiliacao.user',\Auth::user()->id)
            ->whereIn('solicitacao_afiliacao.status',['Pendente','Negada'])
            ->select([
                'solicitacao_afiliacao.id',
                'solicitacao_afiliacao.projeto',
                'solicitacao_afiliacao.status',
                'projeto.nome',
                'solicitacao_afiliacao.created_at as data_solicitacao',
        ]);

        return Datatables::of($solicitacoes_afiliacoes)
        ->editColumn('data_solicitacao', function($solicitacao_afiliacao){
            return Carbon::parse($solicitacao_afiliacao->data_solicitacao)->format('d/m/Y H:i');
        })
        ->addColumn('detalhes', function ($solicitacao_afiliacao) {
            return "<span data-toggle='modal' data-target='#modal_cancelar_solicitacao'>
                        <a class='cancelar_solicitacao btn btn-outline btn-danger' data-placement='top' data-toggle='tooltip' title='cancelar solicitação' solicitacao_afiliacao='".Hashids::encode($solicitacao_afiliacao->id)."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                            Cancelar solicitação
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);

    }

    public function getAfiliadosProjeto($id_projeto){

        $projeto = Projeto::where('id',$id_projeto)->first();

        $afiliados = Afiliado::where('projeto',$id_projeto)->get()->toArray();

        foreach($afiliados as &$afiliado){
            $usuario = User::find($afiliado['user']);
            $afiliado['nome'] = $usuario['name'];
        }

        $view = view('afiliados::afiliados_projeto',[
            'projeto' => $projeto,
            'afiliados' => $afiliados
        ]);

        return response()->json($view->render());

    }

    public function setEmpresaAfiliacao(Request $request){

        $dados = $request->all();

        $afiliado = Afiliado::where('id',$dados['afiliado'])->first();
        $afiliado->update($dados);

        return response()->json('sucesso');
    }

    public function cancelarSolicitacao(Request $request){

        $dados = $request->all();

        $solicitacao = SolicitacaoAfiliacao::where('id',Hashids::decode($dados['id_solicitacao']))->first();
        $solicitacao->delete();

        return response()->json('sucesso');
    }

    public function negarSolicitacao(Request $request){

        $dados = $request->all();

        $solicitacao = SolicitacaoAfiliacao::where('id',Hashids::decode($dados['id_solicitacao']))->first();

        $solicitacao->update([
            'status' => 'Negada'
        ]);

        return response()->json('sucesso');
    }

    function randString($size){

        $novo_parametro = false;

        while(!$novo_parametro){

            $basic = 'abcdefghijlmnopqrstuvwxyz0123456789';

            $parametro = "";

            for($count= 0; $size > $count; $count++){
                $parametro.= $basic[rand(0, strlen($basic) - 1)];
            }

            $novo_link = LinkAfiliado::where('parametro', $parametro)->first();

            if($novo_link == null){
                $novo_parametro = true;
            }

        }

        return $parametro;
    }

}
