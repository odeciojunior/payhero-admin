<?php

namespace Modules\Usuario\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class UsuarioApiController extends Controller {

    public function informacoesBasicas() {

        $user = User::select('name','foto')->where('id',4)->first();

        return response()->json([
            'user' => $user
        ]);
    }

    public function perfil(){

        $user = User::select(
            'name',
            'email',
            'cpf',
            'celular',
            'telefone1',
            'data_nascimento',
            'foto',
            'cep',
            'pais',
            'estado',
            'cidade',
            'bairro',            
            'logradouro',
            'numero',
            'complemento'
        )->where('id',4)->first();

        return response()->json([
            'user' => $user
        ]);

    }

    public function alterarSenha(Request $request){

        $dados = $request->all();

        User::find(\Auth::user()->id)->update([
            'password' => bcrypt($dados['senha'])
        ]);

        return response()->json('sucesso');
    }

    public function update(Request $request){

        $dados = $request->all();

        return response()->json($dados);

    }


}
