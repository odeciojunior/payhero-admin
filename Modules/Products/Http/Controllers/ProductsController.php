<?php

namespace Modules\Products\Http\Controllers;

use App\Entities\Product;
use App\Entities\Company;
use App\Entities\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class ProductsController extends Controller {

    public function index(Request $request) {

        $products = Product::where('user',\Auth::user()->id)->where('shopify', '0');

        if(isset($request->nome)){
            $products = $products->where('nome','LIKE','%'.$request->nome.'%');
        }
        
        $products = $products->orderBy('id','DESC')->paginate(12);
 
        return view('products::index',[
            'products' => $products
        ]);
    }

    public function create() {

        $categories = Category::all();

        return view('products::create',[
            'categorias' => $categories,
        ]);
    }

    public function store(Request $request){

        $requestData = $request->all();

        $requestData['user'] = \Auth::user()->id;

        $product = Product::create($requestData);

        $photo = $request->file('product_photo');

        if ($photo != null) {
            try{
                $photoName = 'produto_' . $product->id . '_.' . $photo->getClientOriginalExtension();

                Storage::delete('public/upload/produto/'.$photoName);

                $photo->move(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO, $photoName);

                $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $photoName);

                $img->crop($requestData['foto_w'], $requestData['foto_h'], $requestData['foto_x1'], $requestData['foto_y1']);

                $img->resize(200, 200);

                Storage::delete('public/upload/produto/'.$photoName);
                
                $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $photoName);

                $product->update([
                    'photo' => $photoName
                ]);
            }
            catch(\Exception $e){
                //
            }
        }

        return redirect()->route('products');
    }

    public function edit($id){

        $product = Product::find($id);
        $categories = Category::all();

        return view('products::edit',[
            'product'    => $product,
            'categories' => $categories
        ]);

    }

    public function update(Request $request){

        $requestData = $request->all();

        if($request->file('foto') == null){
            unset($requestData['foto']);
        }

        $product = Product::find($requestData['id']);
        $product->update($requestData);

        $photo = $request->file('foto_produto');

        if ($photo != null) {

            try{
                $photoName = 'produto_' . $product->id . '_.' . $photo->getClientOriginalExtension();

                Storage::delete('public/upload/produto/'.$photoName);

                $photo->move(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO, $photoName);

                $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $photoName);

                $img->crop($requestData['foto_w'], $requestData['foto_h'], $requestData['foto_x1'], $requestData['foto_y1']);

                $img->resize(200, 200);

                Storage::delete('public/upload/produto/'.$photoName);
                
                $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $photoName);

                $product->update([
                    'photo' => $photoName
                ]);
            }
            catch(\Exception $e){
                //
            }

        }

        return redirect()->route('products');
    }

    public function delete($id){

        Product::find($id)->delete();

        return redirect()->route('products');

    }

    public function details(Request $request){

        $requestData = $request->all();

        $product = Product::find($requestData['id_produto']);

        $modalBody = '';

        $modalBody .= "<div class='col-xl-12 col-lg-12'>";
        $modalBody .= "<table class='table table-bordered table-hover table-striped'>";
        $modalBody .= "<thead>";
        $modalBody .= "</thead>";
        $modalBody .= "<tbody>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Nome:</b></td>";
        $modalBody .= "<td>".$product->name."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Descrição:</b></td>";
        $modalBody .= "<td>".$product->description."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Formato:</b></td>";
        if($product->format == 1)
            $modalBody .= "<td>Físico</td>";
        else
            $modalBody .= "<td>Digital</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Categoria:</b></td>";
        $modalBody .= "<td>".Category::find($product->category)->name."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Garantia:</b></td>";
        $modalBody .= "<td>".$product->guarantee."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Quantidade:</b></td>";
        $modalBody .= "<td>".$product->amount."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Custo do produto:</b></td>";
        $modalBody .= "<td>".$product->cost."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Altura:</b></td>";
        $modalBody .= "<td>".$product->height."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Largura:</b></td>";
        $modalBody .= "<td>".$product->width."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Peso:</b></td>";
        $modalBody .= "<td>".$product->weight."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "</thead>";
        $modalBody .= "</table>";
        $modalBody .= "</div>";
        $modalBody .= "<div class='text-center' style='margin-top: 20px'>";
        $modalBody .= "<img src='".'/'.CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO.$product['photo']."' style='height: 200px'>";
        $modalBody .= "</div>";
        $modalBody .= "</div>";

        return response()->json($modalBody);
    }

}


