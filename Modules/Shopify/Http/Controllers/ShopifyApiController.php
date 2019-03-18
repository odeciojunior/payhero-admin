<?php

namespace Modules\Shopify\Http\Controllers;

use App\IntegracaoShopify;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Shopify\Transformers\IntegracoesShopifyResource;

class ShopifyApiController extends Controller {

    public function integracoes() {

        $integracoes_shopify = IntegracaoShopify::where('user',\Auth::user()->id);

        return IntegracoesShopifyResource::collection($integracoes_shopify->paginate());
    }

}
