<?php

namespace Modules\Perfil\Http\Controllers;

use App\User;
use App\Empresa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Routing\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
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

        $foto = $request->file('foto_usuario');

        if ($foto != null) {

            try{
                $nomeFoto = 'user_' . $user->id . '_.' . $foto->getClientOriginalExtension();

                Storage::delete('public/upload/perfil/'.$nomeFoto);
                
                $foto->move(CaminhoArquivosHelper::CAMINHO_FOTO_USER, $nomeFoto);

                $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_USER . $nomeFoto);

                $img->crop($dados['foto_w'], $dados['foto_h'], $dados['foto_x1'], $dados['foto_y1']);

                $img->resize(200, 200);

                Storage::delete('public/upload/perfil/'.$nomeFoto);
                
                $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_USER . $nomeFoto);

                $user->update([
                    'foto' => $nomeFoto
                ]);
            }
            catch(\Exception $e){
                //
            }
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


