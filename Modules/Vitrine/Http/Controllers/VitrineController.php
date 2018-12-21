<?php

namespace Modules\Vitrine\Http\Controllers;

use App\User;
use App\Empresa;
use App\Projeto;
use App\Afiliado;
use App\UserProjeto;
use App\UsuarioEmpresa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class VitrineController extends Controller {

    public function index() {

        $afiliacoes_usuario = Afiliado::where('user',\Auth::user()->id)->pluck('projeto')->toArray();
        $projetos_disponiveis = UserProjeto::where('user','!=',\Auth::user()->id)->pluck('projeto')->toArray();

        $projetos = Projeto::whereIn('id', $projetos_disponiveis)
                            ->whereNotIn('id',$afiliacoes_usuario)
                            ->get()->toArray();

        foreach($projetos as &$projeto){
            $projeto_usuario = UserProjeto::where([
                ['projeto',$projeto['id']],
                ['tipo','produtor']
            ])->first();
            $usuario = User::find($projeto_usuario['user']);
            $projeto['produtor'] = $usuario['name'];
        }

        return view('vitrine::index',[
            'projetos' => $projetos
        ]); 
    }

}


