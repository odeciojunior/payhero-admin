<?php

namespace Modules\Cadastro\Http\Controllers;

use App\User;
use App\Convite;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class CadastroController extends Controller {

    public function cadastro($parametro) {

        $convite = Convite::where('parametro',$parametro)->first();

        if($convite == null){
            echo 'convite não encontrado';
            die;
        }

        return view('cadastro::cadastro', [
            'convite' => $convite
        ]);
    }

    public function novoUsuario(Request $request){

        $dados = $request->all();

        $convite = Convite::find($dados['id_convite']);

        $user = User::where('email', $dados['email'])->first();

        if($user != null){
            return view('cadastro::cadastro', [
                'convite' => $convite,
                'erro' => 'Email já esta sendo utilizado'
            ]);
        }

        $dados['password'] = bcrypt($dados['password']);

        $dados['taxa_porcentagem'] = '6.9';

        $user = User::create($dados);

        $user->assignRole('administrador empresarial');

        $convite->update([
            'user_convidado' => $user->id,
            'status' => 'Ativo',
            'data_cadastro' => Carbon::now()->format('Y-m-d'),
            'data_expiracao' => Carbon::now()->addMonths(6)->format('Y-m-d'),
            'email_convidado' => $dados['email'],
        ]);

        return response()->json('sucesso');
    }

}
