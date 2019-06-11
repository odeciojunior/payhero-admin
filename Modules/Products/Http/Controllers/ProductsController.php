<?php

namespace Modules\Products\Http\Controllers;

use App\Entities\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{
    /**
     * @var Product
     */
    private $productModel;

    /**
     * ProductsController constructor.
     * @param Product $product
     */
    function __construct(Product $product)
    {
        $this->productModel = $product;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            if (isset($request->nome)) {
                $productsSearch = $this->productModel->where('name', 'LIKE', '%' . $request->nome . '%')
                                                     ->where('user', auth()->user()->id)
                                                     ->where('shopify', 0);
            } else {
                $productsSearch = $this->productModel->where('user', auth()->user()->id)->where('shopify', 0);
            }

            $products = $productsSearch->orderBy('id', 'DESC')->paginate(12);

            return view('products::index', ['products' => $products]);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar produtos (ProductsController - index)');
            report($e);
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        try {
            return view('products::create');
        } catch (Exception $e) {
            Log::warning('Erro ao tenta acessar pagina de cadastro de produto (ProductsController - create)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $data         = $request->all();
            $data['user'] = auth()->user()->id;

            $product = $this->productModel->create($data);
            //            $photo   = $request->file('product_photo');

            /*if (!is_null($photo)) {
                try {
                    $photoName = 'produto_' . $product->id . '_.' . $photo->getClientOriginalExtension();
                    Storage::delete('public/upload/produto/' . $photoName);
                    $photo->move(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO, $photoName);
                    $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO, $photoName);
                    $img->crop($data['foto_w'], $data['foto_h'], $data['foto_x1'], $data['foto_y1']);
                    $img->resize(200, 200);
                    Storage::delete('public/upload/produto/' . $photoName);
                    $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO, $photoName);
                    $product->update(['photo' => $photoName]);
                } catch (Exception $e) {
                    Log::warning('Erro ao salvar imagem do produto (ProductsController - store');
                    report($e);
                }
            }*/

            return redirect()->route('products.index');
        } catch (Exception $e) {
            Log::error('Erro ao tentar cadastrar produto (ProductsController - store)');
            report($e);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $product = $this->productModel->find($id);

            return view('products::edit', ['product' => $product]);
        } catch (Exception $e) {
            Log::error('Erro ao tentar acessar tela de editar produto (ProductsController - edit)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        try {
            $data    = $request->all();
            $product = $this->productModel->find($data['id']);
            $product->update($data);

            return redirect()->route('products.index');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar atualizar produto (ProductsController - update)');
            report($e);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $id      = intval($id);
            $product = $this->productModel->find($id);
            if ($product) {
                $product->delete();
            }

            return redirect()->route('products.index');
        } catch (Exception $e) {
            Log::error('Erro ao tentar excluir produto (ProductsController - delete)');
            report($e);
        }
    }

    /**
     * nao esta sendo utilizado mais
     */
    /*public function details(Request $request)
    {

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
        $modalBody .= "<td>" . $product->name . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Descrição:</b></td>";
        $modalBody .= "<td>" . $product->description . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Formato:</b></td>";
        if ($product->format == 1)
            $modalBody .= "<td>Físico</td>";
        else
            $modalBody .= "<td>Digital</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Categoria:</b></td>";
        $modalBody .= "<td>" . Category::find($product->category)->name . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Garantia:</b></td>";
        $modalBody .= "<td>" . $product->guarantee . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Quantidade:</b></td>";
        $modalBody .= "<td>" . $product->amount . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Custo do produto:</b></td>";
        $modalBody .= "<td>" . $product->cost . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Altura:</b></td>";
        $modalBody .= "<td>" . $product->height . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Largura:</b></td>";
        $modalBody .= "<td>" . $product->width . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Peso:</b></td>";
        $modalBody .= "<td>" . $product->weight . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "</thead>";
        $modalBody .= "</table>";
        $modalBody .= "</div>";
        $modalBody .= "<div class='text-center' style='margin-top: 20px'>";
        $modalBody .= "<img src='" . '/' . CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $product['photo'] . "' style='height: 200px'>";
        $modalBody .= "</div>";
        $modalBody .= "</div>";

        return response()->json($modalBody);
    }*/
}


