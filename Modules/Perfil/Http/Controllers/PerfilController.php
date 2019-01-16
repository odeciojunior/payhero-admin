<?php

namespace Modules\Perfil\Http\Controllers;

use App\User;
use App\Empresa;
use App\UsuarioEmpresa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class PerfilController extends Controller {

    public function index() {

        $user = \Auth::user();

        return view('perfil::index', [
            'user' => $user,
        ]);

    }

    public function update(Request $request) {

        $dados = $request->all();

        $user = User::find($dados['id']);

        $user->update($dados);

        $foto = $request->file('foto');

        if ($foto != null) {
            $nome_foto = 'user_' . $user->id . '_.' . $foto->getClientOriginalExtension();

            $foto->move(CaminhoArquivosHelper::CAMINHO_FOTO_USER, $nome_foto);

            $user->update([
                'foto' => $nome_foto
            ]);
        }

        return redirect()->route('perfil');
    }

    public function alterarSenha(Request $request){

        $dados = $request->all();

        $user = \Auth::user();

        $user->update([
            'password' => bcrypt($dados['nova_senha'])
        ]);

        return response()->json("sucesso");

    }


}


