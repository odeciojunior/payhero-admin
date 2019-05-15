<?php

namespace Modules\Pixels\Http\Controllers;

use App\Pixel;
use App\Projeto;
use App\Entities\UserProjeto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Pixels\Transformers\PixelsResource;

class PixelsApiController extends Controller {

    public function index(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $pixels = Pixel::select('id','nome','cod_pixel','plataforma','status')
                        ->where('projeto','=', $projeto['id']);

        return PixelsResource::collection($pixels->paginate(10));
    }

    public function store(Request $request) {

        $dados = $request->all();
        $projeto = Projeto::find(Hashids::decode($request->projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $dados['projeto'] = $projeto['id'];

        Pixel::create($dados);

        return response()->json('sucesso');
    }

    public function show(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $pixel = Pixel::select(
            'nome',
            'cod_pixel',
            'plataforma',
            'status',
            'checkout',
            'purchase_cartao',
            'purchase_boleto'
        )->where('id',Hashids::decode($request->id_pixel)->first());

        if(!$pixel){
            return response()->json('pixel não encontrado');
        }

        return response()->json($pixel);
    }

    public function update(Request $request) {

        $dados = $request->all();

        $projeto = Projeto::find(Hashids::decode($request->projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $pixel = Pixel::find(Hashids::decode($dados['id']));
        
        if(!$pixel){
            return reponse()->json('pixel não encontrado');
        }

        $pixel->update($dados);

        return response()->json('sucesso');
    }

    public function destroy(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $pixel = Pixel::find(Hashids::decode($request->id_pixel));
        
        if(!$pixel){
            return response()->json('pixel não encontrado');
        }

        $pixel->delete();

        return response()->json('sucesso');
    }

    public function isAuthorized($id_projeto){

        $projeto_usuario = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor'],
            ['projeto', $id_projeto]
        ])->first();

        if(!$projeto_usuario){
            return false;
        }

        return true;
    }
}
