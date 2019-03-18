<?php

namespace Modules\Afiliados\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class AfiliadosApiController extends Controller {


    public function meusAfiliados() {

        return response()->json('foii meusAfiliados');
    }

    public function meusAfiliadosSolicitacoes() {

        return response()->json('foii meusAfiliadosSolicitacoes');
    }

    public function minhasAfiliacoes() {

        return response()->json('foii minhasAfiliacoes');
    }

    public function minhasAfiliacoesSolicitacoes() {

        return response()->json('foii minhasAfiliacoesSolicitacoes');
    }

}
