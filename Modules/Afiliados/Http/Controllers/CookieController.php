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
use Illuminate\Support\Facades\Cookie;

class CookieController extends Controller {

    public function setCookie($parametro) {

        $link_afiliado = LinkAfiliado::where('parametro',$parametro)->first();

        $afiliado = Afiliado::find($link_afiliado['afiliado']);

        if($link_afiliado['plano'] != ''){

            $plano = Plano::find($link_afiliado['plano']);

            $dominio = Dominio::where('projeto',$afiliado['projeto'])->first();

            $url = 'https://checkout.'.$dominio['dominio'].'/'.$plano['cod_identificador'];
        }
        else{

            $projeto = Projeto::find($afiliado['projeto']);

            $url = 'http://'.$projeto['url_pagina'];
        }

        // Cookie::queue('affiliate_cf', $afiliado['id'], time() + 60 * 60 * 24 * 1);

        $view = view('afiliados::cookie_redirect', [
            'url' => $url
        ]);

        $response = new Response($view);

        return $response->cookie('affiliate_cf', $afiliado['id'], time() + 60 * 60 * 24 * 1);

    }


}
