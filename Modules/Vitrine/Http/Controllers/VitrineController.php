<?php

namespace Modules\Vitrine\Http\Controllers;

use App\User;
use App\Empresa;
use App\Projeto;
use App\Afiliado;
use App\UsuarioEmpresa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class VitrineController extends Controller {

    public function index() {

        $empresas_usuario = UsuarioEmpresa::where('user',\Auth::user()->id)->pluck('empresa')->toArray();
        $afiliacoes_usuario = Afiliado::where('user',\Auth::user()->id)->pluck('projeto')->toArray();

        $projetos = Projeto::where('visibilidade', 'publico')
                            ->whereNotIn('empresa', $empresas_usuario)
                            ->whereNotIn('id',$afiliacoes_usuario)
                            ->get()->toArray();

        foreach($projetos as &$projeto){

            $empresas_usuario = UsuarioEmpresa::where('empresa',$projeto['empresa'])->first();
            $usuario = User::find($empresas_usuario['user']);
            $projeto['produtor'] = $usuario['name'];

        }

        return view('vitrine::index',[
            'projetos' => $projetos
        ]); 
    }

}


