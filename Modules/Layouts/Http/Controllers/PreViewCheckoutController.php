<?php

namespace Modules\Layouts\Http\Controllers;

use App\Foto;
use App\Plano;
use App\Layout;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Intervention\Image\Facades\Image;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class PreViewCheckoutController extends Controller {

    public function checkoutPreView(Request $request) {

        $dados = $request->all();

        $logo = $request->file('foto_checkout');

        if($logo != null){
            $mime = mime_content_type($_FILES['foto_checkout']['tmp_name']);
            $data = file_get_contents($_FILES['foto_checkout']['tmp_name']);
            $logo = 'data:' . $mime . ';base64,' . base64_encode($data);
            $img = Image::make(file_get_contents($logo));
            $img->crop($dados['preview_logo_w'], $dados['preview_logo_h'], $dados['preview_logo_x1'], $dados['preview_logo_y1']);
            if($dados['logo_formato'] == 'quadrado')
                $img->resize(150, 150);
            else
                $img->resize(300, 150);
            $logo = $img->encode('data-url');
        }
        elseif($dados['tipo'] == 'editar'){
            $layout = Layout::find($dados['layout']);
            $logo = '/'.CaminhoArquivosHelper::CAMINHO_FOTO_LOGO.$layout["logo"];
        }

        $plano = Plano::where('id', '19')->first();

        $foto = '/'.CaminhoArquivosHelper::CAMINHO_FOTO_PLANO.$plano->foto;

        return view('layouts::checkout_pre_view', [
                'logo' => $logo,
                'plano' => $plano,  
                'foto' => $foto,
            ]
        );
    }


}
