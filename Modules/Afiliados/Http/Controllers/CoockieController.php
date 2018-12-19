<?php

namespace Modules\Afiliados\Http\Controllers;

use App\Plano;
use App\Dominio;
use App\Projeto;
use App\Afiliado;
use App\LinkAfiliado;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class CoockieController extends Controller {

    public function setCoockie($parametro) {

        $link_afiliado = LinkAfiliado::where('parametro',$parametro)->first();

        $afiliado = Afiliado::find($link_afiliado['afiliado']);

        if($link_afiliado['plano'] != ''){

            $plano = Plano::find($link_afiliado['plano']);
    
            $dominio = Dominio::where('projeto',$afiliado['projeto'])->first();
    
            return redirect('checkout.'.$dominio['dominio'].'/'.$plano['cod_identificador'])->cookie('affiliate_cf', $afiliado['id'], time() + 60 * 60 * 24 * 1);
        }
        else{

            $projeto = Projeto::find($afiliado['projeto']);

            return redirect()->away('http://'.$projeto['url_pagina'])->cookie('affiliate_cf', $afiliado['id'], time() + 60 * 60 * 24 * 1);
        }
    }


}
