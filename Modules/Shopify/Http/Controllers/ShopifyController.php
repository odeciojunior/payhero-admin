<?php

namespace Modules\Shopify\Http\Controllers;

use Exception;
use App\Entities\Plan;
use App\Entities\Company;
use App\Entities\Product;
use App\Entities\Project;
use Slince\Shopify\Client;
use Illuminate\Http\Request;
use App\Entities\ProductPlan;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Adapter\Guzzle;
use Illuminate\Routing\Controller;
use Cloudflare\API\Endpoints\Zones;
use Illuminate\Support\Facades\Log;
use App\Entities\ShopifyIntegration;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Slince\Shopify\PublicAppCredential;
use Modules\Core\Helpers\CaminhoArquivosHelper;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\Tag;
use PHPHtmlParser\Dom\TextNode;
use Vinkla\Hashids\Facades\Hashids;
use PHPHtmlParser\Dom;

class ShopifyController extends Controller
{
    public function index()
    {

        $companies = Company::where('user', \Auth::user()->id)->get()->toArray();

        $shopifyIntegrations = ShopifyIntegration::where('user', \Auth::user()->id)->get()->toArray();

        $projects = [];

        foreach ($shopifyIntegrations as $shopifyIntegration) {

            $project = Project::find($shopifyIntegration['project']);

            if ($project) {
                $projects[] = $project;
            }
        }

        return view('shopify::index', [
            'companies' => $companies,
            'projects'  => $projects,
        ]);
    }

    public function adicionarIntegracao(Request $request)
    {

        $dados = $request->all();

        $shopifyIntegration = ShopifyIntegration::where('token', $dados['token'])->first();

        if ($shopifyIntegration != null) {
            return response()->json('Projeto já integrado');
        }

        try {
            $credential = new PublicAppCredential($dados['token']);

            $client = new Client($credential, $dados['url_store'], [
                'metaCacheDir' => './tmp' // Metadata cache dir, required
            ]);
        } catch (\Exception $e) {
            return response()->json('Dados do shopify inválidos, revise os dados informados');
        }

        try {
            $project = Project::create([
                                           'name'                  => $client->getShopManager()->get()->getName(),
                                           'status'                => '1',
                                           'visibility'            => 'private',
                                           'percentage_affiliates' => '0',
                                           'description'           => $client->getShopManager()->get()->getName(),
                                           'invoice_description'   => $client->getShopManager()->get()->getName(),
                                           'url_page'              => 'https://' . $client->getShopManager()->get()
                                                                                          ->getDomain(),
                                           'automatic_affiliation' => false,
                                           'shopify_id'            => $client->getShopManager()->get()->getId(),
                                       ]);
        } catch (\Exception $e) {
            return response()->json('Dados do shopify inválidos, revise os dados informados');
        }

        $themes = $client->getThemeManager()->findAll([]);

        foreach ($themes as $theme) {
            if ($theme->getRole() == 'main')
                break;
        }

        $htmlCart = $client->getAssetManager()->find($theme->getId(), "sections/cart-template.liquid");

        $asset = $client->getAssetManager()->update($theme->getId(), [
            "key"   => "sections/cart-template.liquid",
            "value" => $this->getCartTemplate($htmlCart->getValue()),
        ]);

        $photo = $request->file('foto_projeto');

        if ($photo != null) {
            $photoName = 'projeto_' . $project->id . '_.' . $photo->getClientOriginalExtension();

            $photo->move(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO, $photoName);

            try {
                $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO . $photoName);

                $img->crop($dados['foto_w'], $dados['foto_h'], $dados['foto_x1'], $dados['foto_y1']);

                $img->resize(200, 200);

                Storage::delete('public/upload/projeto/' . $photoName);

                $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO . $photoName);

                $project->update([
                                     'photo' => $photoName,
                                 ]);
            } catch (\Exception $e) {
                // não cadastra imagem
            }
        }

        UserProject::create([
                                'user'                 => \Auth::user()->id,
                                'project'              => $project->id,
                                'company'              => $dados['company'],
                                'type'                 => 'producer',
                                'shipment_responsible' => true,
                                'permissao_acesso'     => true,
                                'permissao_editar'     => true,
                                'status'               => 'active',
                            ]);

        $products = $client->getProductManager()->findAll([]);

        foreach ($products as $shopifyProduct) {

            foreach ($shopifyProduct->getVariants() as $variant) {

                $product = Product::create([
                                               'user'        => \Auth::user()->id,
                                               'name'        => substr($shopifyProduct->getTitle(), 0, 100),
                                               'description' => '',
                                               'guarantee'   => '0',
                                               'format'      => 1,
                                               'category'    => '11',
                                               'cost'        => '',
                                               'shopify'     => true,
                                           ]);

                $newCode = false;

                while ($newCode == false) {

                    $code = $this->randString(3) . rand(100, 999);

                    $plan = Plan::where('code', $code)->first();

                    if ($plan == null) {
                        $newCode = true;
                    }
                }

                $description = '';

                try {
                    $description = $variant->getOption1();
                    if ($description == 'Default Title') {
                        $description = '';
                    }
                    if ($variant->getOption2() != '') {
                        $description .= ' - ' . $$variant->getOption2();
                    }
                    if ($variant->getOption3() != '') {
                        $description .= ' - ' . $$variant->getOption3();
                    }
                } catch (\Exception $e) {
                    //
                }

                $plan = Plan::create([
                                         'shopify_id'         => $shopifyProduct->getId(),
                                         'shopify_variant_id' => $variant->getId(),
                                         'company'            => $dados['company'],
                                         'project'            => $project->id,
                                         'name'               => substr($shopifyProduct->getTitle(), 0, 100),
                                         'description'        => $description,
                                         'code'               => $code,
                                         'price'              => $variant->getPrice(),
                                         'status'             => '1',
                                     ]);

                if (count($shopifyProduct->getVariants()) > 1) {
                    foreach ($shopifyProduct->getImages() as $image) {

                        foreach ($image->getVariantIds() as $variantId) {
                            if ($variantId == $variant->getId()) {

                                if ($image->getSrc() != '') {
                                    $product->update([
                                                         'photo' => $image->getSrc(),
                                                     ]);

                                    $plan->update([
                                                      'photo' => $image->getSrc(),
                                                  ]);
                                } else {
                                    $plan->update([
                                                      'photo' => $shopifyProduct->getImage()->getSrc(),
                                                  ]);

                                    $product->update([
                                                         'photo' => $shopifyProduct->getImage()->getSrc(),
                                                     ]);
                                }
                            }
                        }
                    }
                } else {

                    $plan->update([
                                      'photo' => $shopifyProduct->getImage()->getSrc(),
                                  ]);

                    $product->update([
                                         'photo' => $shopifyProduct->getImage()->getSrc(),
                                     ]);
                }

                ProductPlan::create([
                                        'product' => $product->id,
                                        'plan'    => $plan->id,
                                        'amount'  => '1',
                                    ]);
            }
        }

        $client->getWebhookManager()->create([
                                                 "topic"   => "products/create",
                                                 "address" => "https://cloudfox.app/aplicativos/shopify/webhook/" . $project['id'],
                                                 "format"  => "json",
                                             ]);

        $client->getWebhookManager()->create([
                                                 "topic"   => "products/update",
                                                 "address" => "https://cloudfox.app/aplicativos/shopify/webhook/" . $project['id'],
                                                 "format"  => "json",
                                             ]);

        ShopifyIntegration::create([
                                       'token'     => $dados['token'],
                                       'url_store' => $dados['url_store'],
                                       'user'      => \Auth::user()->id,
                                       'project'   => $project->id,
                                   ]);

        return response()->json('Sucesso');
    }

    public function getCartTemplate($htmlCart)
    {

        $dom = new Dom;
        $dom->load($htmlCart);

        $form = $dom->find('form');
        $form->setAttribute('action', 'https://checkout.{{ shop.domain }}/');
        $form->setAttribute('id', 'cart_form');

        $inputHidden = new HtmlNode('input');
        $inputHidden->setAttribute('type', 'hidden');
        $inputHidden->setAttribute('name', 'product_id_{{ forloop.index }}');
        $inputHidden->setAttribute('value', '{{ item.id }}');
        $form->addChild($inputHidden);

        $inputHidden = new HtmlNode('input');
        $inputHidden->setAttribute('type', 'hidden');
        $inputHidden->setAttribute('name', 'variant_id_{{ forloop.index }}');
        $inputHidden->setAttribute('value', '{{ item.variant_id }}');
        $form->addChild($inputHidden);

        $inputHidden = new HtmlNode('input');
        $inputHidden->setAttribute('type', 'hidden');
        $inputHidden->setAttribute('name', 'product_price_{{ forloop.index }}');
        $inputHidden->setAttribute('value', '{{ item.price }}');
        $form->addChild($inputHidden);

        $inputHidden = new HtmlNode('input');
        $inputHidden->setAttribute('type', 'hidden');
        $inputHidden->setAttribute('name', 'product_image_{{ forloop.index }}');
        $inputHidden->setAttribute('value', '{{ item.image }}');
        $form->addChild($inputHidden);

        $inputHidden = new HtmlNode('input');
        $inputHidden->setAttribute('type', 'hidden');
        $inputHidden->setAttribute('name', 'product_amount_{{ forloop.index }}');
        $inputHidden->setAttribute('value', '{{ item.quantity }}');
        $form->addChild($inputHidden);

        $script = new HtmlNode('script');
        $script->addChild(new TextNode("$(document).ready(function (){

                    var first_time = true;

                    $('.wh-original-cart-total').on('DOMSubtreeModified',function(){

                      if(first_time && typeof($(this).find($(\"[data-integration-price-saved=1].wh-original-price\")).html()) != 'undefined'){
                        var discount = $(this).find($(\"[data-integration-price-saved=1].wh-original-price\")).html().replace(/[^0-9]/g,'');

                        if(discount != '' && discount > 10){
                          $('#cart_form').append(\"<input type='hidden' name='value_discount' value='\"+discount+\"'>\");
                          first_time = false;
                        }

                      }

                    });

                  });
        "));
        $form->addChild($script);

        return $dom->root->outerHtml();

    }

    public function webHook(Request $request)
    {

        $dados = $request->all();
        // Log::write('info', 'retorno do shopify ' . print_r($dados, true) );
        // return 'success';

        $project = Project::find($request->id_projeto);

        if (!$project) {
            Log::write('info', 'projeto não encontrado no retorno do shopify, projeto = ' . $request->id_projeto);

            return 'error';
        } else {
            Log::write('info', 'retorno do shopify, projeto = ' . $request->id_projeto);
        }

        foreach ($dados['variants'] as $variant) {

            $plan = Plan::where([
                                    ['shopify_variant_id', $variant['id']],
                                    ['project', $request->id_projeto],
                                ])->first();

            $description = '';
            try {
                $description = $variant['option1'];
                if ($description == 'Default Title') {
                    $description = '';
                }
                if ($variant['option2'] != '') {
                    $description .= ' - ' . $$variant['option2'];
                }
                if ($variant['option3'] != '') {
                    $description .= ' - ' . $$variant['option3'];
                }
            } catch (\Exception $e) {
                report($e);
            }

            if ($plan) {
                $plan->update([
                                  'name'        => substr($dados['title'], 0, 100),
                                  'price'       => $variant['price'],
                                  'description' => $description,
                              ]);
            } else {
                $userProject = UserProject::where([
                                                      ['project', $project['id']],
                                                      ['type', 'producer'],
                                                  ])->first();

                $product = Product::create([
                                               'user'        => $userProject->user,
                                               'name'        => substr($dados['title'], 0, 100),
                                               'description' => $description,
                                               'guarantee'   => '0',
                                               'available'   => true,
                                               'amount'      => '0',
                                               'format'      => 1,
                                               'category'    => 11,
                                               'cost'        => '',
                                           ]);

                $newCode = false;

                while ($newCode == false) {
                    $code = $this->randString(3) . rand(100, 999);
                    $plan = Plan::where('code', $code)->first();
                    if ($plan == null) {
                        $newCode = true;
                    }
                }

                $plan = Plan::create([
                                         'shopify_id'                 => $dados['id'],
                                         'shopify_variant_id'         => $variant['id'],
                                         'company'                    => $userProject->company,
                                         'project'                    => $project['id'],
                                         'name'                       => substr($dados['title'], 0, 100),
                                         'description'                => $description,
                                         'code'                       => $code,
                                         'price'                      => $variant['price'],
                                         'status'                     => '1',
                                         'carrier'                    => '2',
                                         'installments_amount'        => '12',
                                         'installments_interest_free' => '1',
                                     ]);

                if (count($dados['variants']) > 1) {
                    foreach ($dados['images'] as $image) {

                        foreach ($image['variant_ids'] as $variantId) {
                            if ($variantId == $variant['id']) {

                                if ($image['src'] != '') {
                                    $product->update([
                                                         'photo' => $image->getSrc(),
                                                     ]);

                                    $plan->update([
                                                      'photo' => $image->getSrc(),
                                                  ]);
                                } else {
                                    $plan->update([
                                                      'photo' => $dados['image']['src'],
                                                  ]);
                                    $product->update([
                                                         'photo' => $dados['image']['src'],
                                                     ]);
                                }
                            }
                        }
                    }
                } else {
                    $plan->update([
                                      'photo' => $dados['image']['src'],
                                  ]);

                    $product->update([
                                         'photo' => $dados['image']['src'],
                                     ]);
                }

                ProdutoPlan::create([
                                        'product' => $product->id,
                                        'plan'    => $plan->id,
                                        'amount'  => '1',
                                    ]);
            }
        }

        return 'true';
    }

    function randString($size)
    {

        $basic = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $return = "";

        for ($count = 0; $size > $count; $count++) {

            $return .= $basic[rand(0, strlen($basic) - 1)];
        }

        return $return;
    }
}

