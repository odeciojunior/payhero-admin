<?php

namespace Modules\Pixels\Http\Controllers;

use App\Pixel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class PixelsApiController extends Controller {

    public function index(Request $request) {

        $pixels = Pixel::select('id','nome','cod_pixel','plataforma','status')->where('projeto','=', $request->id_projeto);

        return response()->json($pixels->paginate());
    }

    public function store(Request $request) {

        $dados = $request->all();
        $dados['projeto'] = $request->id_projeto;

        Pixel::create($dados);

        return response()->json('sucesso');
    }

    public function show(Request $request) {

        $pixel = Pixel::select(
            'nome',
            'cod_pixel',
            'plataforma',
            'status',
            'checkout',
            'purchase_cartao',
            'purchase_boleto'
        )->where('id',$request->id_pixel)->first();

        return response()->json($pixel);
    }

    public function update(Request $request) {

        $dados = $request->all();

        Pixel::find($dados['id'])->update($dados);

        return response()->json('sucesso');

    }

    public function destroy(Request $request) {

        Pixel::find($request->id_pixel)->delete();

        return response()->json('sucesso');
    }

}
