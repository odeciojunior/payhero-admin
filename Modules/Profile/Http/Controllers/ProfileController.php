<?php

namespace Modules\Profile\Http\Controllers;

use App\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class ProfileController extends Controller {

    public function index() {

        $user = \Auth::user();

        return view('profile::index', [
            'user' => $user,
        ]);

    }

    public function update(Request $request) {

        $requestData = $request->all();

        $user = User::find($requestData['id']);

        $user->update($requestData);

        $userPhoto = $request->file('foto_usuario');
 
        if ($userPhoto != null) {

            try{
                $photoName = 'user_' . $user->id . '_.' . $userPhoto->getClientOriginalExtension();

                Storage::delete('public/upload/perfil/'.$photoName);
                
                $userPhoto->move(CaminhoArquivosHelper::CAMINHO_FOTO_USER, $photoName);

                $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_USER . $photoName);

                $img->crop($requestData['foto_w'], $requestData['foto_h'], $requestData['foto_x1'], $requestData['foto_y1']);

                $img->resize(200, 200);

                Storage::delete('public/upload/perfil/'.$photoName);
                
                $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_USER . $photoName);

                $user->update([
                    'photo' => $photoName
                ]);
            }
            catch(\Exception $e){
                dd($e);
            }
        }

        return redirect()->route('profile');
    }

    public function changePassword(Request $request){

        $requestData = $request->all();

        $user = \Auth::user();

        $user->update([
            'password' => bcrypt($requestData['nova_senha'])
        ]);

        return response()->json("sucesso");
    }

}
