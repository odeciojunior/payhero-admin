<?php

namespace Modules\Layouts\Http\Controllers;

use App\Foto;
use App\Plano;
use App\Layout;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class PreViewCheckoutController extends Controller {

    public function checkoutPreView(Request $request) {

        $dados = $request->all();

        $logo = $request->file('logo');

        if($logo != null){
            $mime = mime_content_type($_FILES['logo']['tmp_name']);
            $data = file_get_contents($_FILES['logo']['tmp_name']);
            $logo = 'data:' . $mime . ';base64,' . base64_encode($data);
        }

        $plano = Plano::where('id', '19')->first();

        $foto = Foto::where('plano', $plano->id)->first();
        $foto = CaminhoArquivosHelper::CAMINHO_FOTO_PLANO.$foto['caminho_imagem'];

        $estilo = $dados['estilo'];
        $cor1 = $dados['cor1'];
        $cor2 = $dados['cor2'];
        $botoes = $dados['botoes'];

        $layout = Layout::find($plano->layout);
        $layout["padrao"] = "";
        $layout["multi"] = "";

        if ($estilo == "Padrao"){
           $layout["padrao"] = "class='".$cor1."'";
        }
        if ($estilo == "Backgound Multi Camada"){
               $layout["multi"] = "<style> .definebg:before { background: ".$cor1.";  } .definebg:after { background: ".$cor2."; } </style>";
        }

        return view('layouts::checkout_pre_view', [
                'layout' => $layout,
                'logo' => $logo,
                'botoes' => $botoes,
                'cor1' => $cor1,
                'cor2' => $cor2,
                'plano' => $plano,  
                'foto' => $foto,
            ]
        );
    }


}
