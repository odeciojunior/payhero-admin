<?php

namespace Modules\Produtos\Http\Controllers;

use App\Produto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Helpers\CaminhoArquivosHelper;
use Modules\Produtos\Transformers\ProdutosResource;

class ProdutosApiController extends Controller {

    public function index() {

        $produtos = Produto::select('id','nome','descricao','foto','created_at')
                            ->where('user',\Auth::user()->id)->orderBy('id','DESC');

        return ProdutosResource::collection($produtos->paginate(10));
    }

    public function store(Request $request) {

        $dados = $request->all();

        $dados['user'] = Auth::user()->id;

        $produto = Produto::create($dados);

        $foto = $request->file('foto_produto');

        if ($foto != null) {
            try{
                $nome_foto = 'produto_' . $produto->id . '_.' . $foto->getClientOriginalExtension();

                Storage::delete('public/upload/produto/'.$nome_foto);

                $foto->move(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO, $nome_foto);

                $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $nome_foto);

                $img->crop($dados['foto_w'], $dados['foto_h'], $dados['foto_x1'], $dados['foto_y1']);

                $img->resize(200, 200);

                Storage::delete('public/upload/produto/'.$nome_foto);
                
                $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $nome_foto);

                $produto->update([
                    'foto' => $nome_foto
                ]);
            }
            catch(\Exception $e){
                //
            }
        }

        return response()->json('sucesso');
    }

    public function show(Request $request) {

        $produto = Produto::select(
            'nome',
            'descricao',
            'categoria',
            'formato',
            'quantidade',
            'foto'
        )->where('id',Hashids::decode($request->id_plano))->first();

        return response()->json($produto);
    }

    public function update(Request $request) {

        $dados = $request->all();

        Produto::find(Hashids::decode($dados['id']))->update($dados);

        return response()->json('sucesso');
    }

    public function destroy(Request $request) {

        Produto::find(Hashids::decode($request->id_produto))->delete();

        return response()->json('sucesso');
    }

}
