<?php

namespace Modules\Cadastro\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class CadastroController extends Controller {

    public function cadastro() {

        return view('cadastro::cadastro');
    }

    public function novoUsuario(Request $request){

        $dados = $request->all();

        $dados['password'] = bcrypt($dados['password']);

        $dados['taxa_porcentagem'] = '6.9';

        $user = User::create($dados);

        $user->assignRole('administrador empresarial');

        return redirect('/');
    }

}
