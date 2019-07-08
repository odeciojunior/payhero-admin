<?php

namespace Modules\Shopify\Http\Controllers;

use Exception;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Core\Services\ShopifyService;
use PHPHtmlParser\Dom;
use App\Entities\Plan;
use App\Entities\Company;
use App\Entities\Product;
use App\Entities\Project;
use Slince\Shopify\Client;
use PHPHtmlParser\Dom\Tag;
use Illuminate\Http\Request;
use App\Entities\ProductPlan;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use Cloudflare\API\Auth\APIKey;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;
use Cloudflare\API\Endpoints\DNS;
use Illuminate\Routing\Controller;
use Cloudflare\API\Adapter\Guzzle;
use PHPHtmlParser\Selector\Parser;
use Cloudflare\API\Endpoints\Zones;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use PHPHtmlParser\Selector\Selector;
use App\Entities\ShopifyIntegration;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Slince\Shopify\PublicAppCredential;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class ShopifyController extends Controller
{
    /**
     * @var DigitalOceanFileService
     */
    private $digitalOceanFileService;
    /**
     * @var ShopifyService
     */
    private $shopifyService;
    /**
     * @var Project
     */
    private $projectModel;
    /**
     * @var ShopifyIntegration
     */
    private $shopifyIntegrationModel;
    /**
     * @var UserProject
     */
    private $userProjectModel;
    /**
     * @var Product
     */
    private $productModel;
    /**
     * @var Plan
     */
    private $planModel;
    /**
     * @var ProductPlan
     */
    private $productPlanModel;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getDigitalOceanFileService()
    {
        if (!$this->digitalOceanFileService) {
            $this->digitalOceanFileService = app(DigitalOceanFileService::class);
        }

        return $this->digitalOceanFileService;
    }

    /**
     * @return ShopifyIntegration|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getShopifyIntegrationModel()
    {
        if (!$this->shopifyIntegrationModel) {
            $this->shopifyIntegrationModel = app(ShopifyIntegration::class);
        }

        return $this->shopifyIntegrationModel;
    }

    /**
     * @return Project|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getProjectModel()
    {
        if (!$this->projectModel) {
            $this->projectModel = app(Project::class);
        }

        return $this->projectModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getPlanModel()
    {
        if (!$this->planModel) {
            $this->planModel = app(Plan::class);
        }

        return $this->planModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getProductPlanModel()
    {
        if (!$this->productPlanModel) {
            $this->productPlanModel = app(ProductPlan::class);
        }

        return $this->productPlanModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getProductModel()
    {
        if (!$this->productModel) {
            $this->productModel = app(Product::class);
        }

        return $this->productModel;
    }

    /**
     * @return UserProject|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getUserProjectModel()
    {
        if (!$this->userProjectModel) {
            $this->userProjectModel = app(UserProject::class);
        }

        return $this->userProjectModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed|ShopifyService
     */
    private function getShopifyService(string $urlStore = null, string $token = null)
    {
        if (!$this->shopifyService) {
            $this->shopifyService = new ShopifyService($urlStore, $token);
        }

        return $this->shopifyService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $companies = Company::where('user_id', \Auth::user()->id)->get()->toArray();

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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function adicionarIntegracao(Request $request)
    {
        try {
            $dados = $request->all();

            $shopifyIntegration = $this->getShopifyIntegrationModel()
                                       ->where('token', $dados['token'])
                                       ->first();

            if (!$shopifyIntegration) {

                try {

                    $shopify = $this->getShopifyService($dados['url_store'] . '.myshopify.com', $dados['token']);
                } catch (\Exception $e) {
                    return response()->json(['message' => 'Dados do shopify inválidos, revise os dados informados'], 400);
                }

                try {

                    $project = $this->getProjectModel()->create([
                                                                    'name'                  => $shopify->getShopName(),
                                                                    'status'                => $this->getProjectModel()
                                                                                                    ->getEnum('status', 'approved'),
                                                                    'visibility'            => 'private',
                                                                    'percentage_affiliates' => '0',
                                                                    'description'           => $shopify->getShopName(),
                                                                    'invoice_description'   => $shopify->getShopName(),
                                                                    'url_page'              => 'https://' . $shopify->getShopDomain(),
                                                                    'automatic_affiliation' => false,
                                                                    'shopify_id'            => $shopify->getShopId(),
                                                                ]);
                } catch (\Exception $e) {
                    return response()->json(['message' => 'Dados do shopify inválidos, revise os dados informados'], 400);
                }

                $shopify->setThemeByRole('main');
                $htmlCart = $shopify->getTemplateHtml('sections/cart-template.liquid');

                if ($htmlCart) {
                    //template normal
                    $shopify->updateTemplateHtml('sections/cart-template.liquid', $htmlCart);
                } else {
                    //template ajax
                    $shopify->updateTemplateHtml('sections/cart-template.liquid', $htmlCart, true);
                }

                $photo = $request->file('photo');

                if ($photo) {

                    try {
                        $img = Image::make($photo->getPathname());
                        $img->crop($dados['photo_w'], $dados['photo_h'], $dados['photo_x1'], $dados['photo_y1']);
                        $img->resize(200, 200);
                        $img->save($photo->getPathname());

                        $digitalOceanPath = $this->getDigitalOceanFileService()
                                                 ->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->id) . '/public/projects/' . $project->id_code . '/main', $photo);

                        $project->update([
                                             'photo' => $digitalOceanPath,
                                         ]);
                    } catch (\Exception $e) {
                        // não cadastra imagem
                    }
                }

                $this->getUserProjectModel()->create([
                                                         'user'                 => auth()->user()->id,
                                                         'project'              => $project->id,
                                                         'company'              => $dados['company'],
                                                         'type'                 => 'producer',
                                                         'shipment_responsible' => true,
                                                         'permissao_acesso'     => true,
                                                         'permissao_editar'     => true,
                                                         'status'               => 'active',
                                                     ]);

                $products = $shopify->getShopProducts();

                foreach ($products as $shopifyProduct) {

                    foreach ($shopifyProduct->getVariants() as $variant) {

                        $description = '';

                        try {
                            $description = $variant->getOption1();
                            if ($description == 'Default Title') {
                                $description = '';
                            }
                            if ($variant->getOption2() != '') {
                                $description .= ' - ' . $variant->getOption2();
                            }
                            if ($variant->getOption3() != '') {
                                $description .= ' - ' . $variant->getOption3();
                            }
                        } catch (\Exception $e) {
                            //
                        }

                        $product = $this->getProductModel()->create([
                                                                        'user'        => auth()->user()->id,
                                                                        'name'        => substr($shopifyProduct->getTitle(), 0, 100),
                                                                        'description' => $description,
                                                                        'guarantee'   => '0',
                                                                        'format'      => 1,
                                                                        'category'    => '11',
                                                                        'cost'        => $shopify->getShopInventoryItem($variant->getInventoryItemId())
                                                                                                 ->getCost(),
                                                                        'shopify'     => true,
                                                                        'price'       => '',
                                                                    ]);

                        $newCode = false;

                        while ($newCode == false) {

                            $code = $this->randString(3) . rand(100, 999);

                            $plan = $this->getPlanModel()->where('code', $code)->first();

                            if ($plan == null) {
                                $newCode = true;
                            }
                        }

                        $plan = $this->getPlanModel()->create([
                                                                  'shopify_id'         => $shopifyProduct->getId(),
                                                                  'shopify_variant_id' => $variant->getId(),
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
                                        } else {

                                            $product->update([
                                                                 'photo' => $shopifyProduct->getImage()->getSrc(),
                                                             ]);
                                        }
                                    }
                                }
                            }
                        } else {

                            $product->update([
                                                 'photo' => $shopifyProduct->getImage()->getSrc(),
                                             ]);
                        }

                        $this->getProductPlanModel()->create([
                                                                 'product' => $product->id,
                                                                 'plan'    => $plan->id,
                                                                 'amount'  => '1',
                                                             ]);
                    }
                }

                $shopify->createShopWebhook([
                                                "topic"   => "products/create",
                                                "address" => "https://app.cloudfox.net/postback/shopify/" . Hashids::encode($project['id']),
                                                "format"  => "json",
                                            ]);

                $shopify->createShopWebhook([
                                                "topic"   => "products/update",
                                                "address" => "https://app.cloudfox.net/postback/shopify/" . Hashids::encode($project['id']),
                                                "format"  => "json",
                                            ]);

                $this->getShopifyIntegrationModel()->create([
                                                                'token'     => $dados['token'],
                                                                'url_store' => $dados['url_store'],
                                                                'user'      => auth()->user()->id,
                                                                'project'   => $project->id,
                                                            ]);

                return response()->json(['message' => 'Integração adicionada!'], 200);
            } else {
                return response()->json(['message' => 'Projeto já integrado'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], 400);
        }
    }

    /*
        public function getCartTemplateAjax($htmlCart)
        {
            $dom = new Dom;

            $dom->setOptions([
                                 'removeScripts' => false,
                             ]);

            $dom->load($htmlCart);

            $forms = $dom->find('script[id=cartTemplate]');
            $x     = $forms->innerHtml();

            //$dom2 = new Dom2;
            $dom->load($x);

            $forms = $dom->find('form');
            foreach ($forms as $form) {
                $data = explode(' ', $form->getAttribute('class'));
                if (in_array('cart', $data) || in_array('cart-form', $data)) {
                    $cartForm = $form;
                    break;
                }
            }

            if ($cartForm) {
                //if ($cartForm->getAttribute('id') != 'cart_form') {

                //div Foxdata
                $divFoxData = new Selector('#foxData', new Parser());
                $divs       = $divFoxData->find($cartForm);
                foreach ($divs as $div) {
                    $parent = $div->getParent();
                    $parent->removeChild($div->id());
                }

                //div FoxScript
                $divFoxScript = new Selector('#foxScript', new Parser());
                $divs         = $divFoxScript->find($cartForm);
                foreach ($divs as $div) {
                    $parent = $div->getParent();
                    $parent->removeChild($div->id());
                }

                //update button
                $inputUpdate   = new Selector('input[name=update]', new Parser());
                $inputsUpdates = $inputUpdate->find($cartForm);
                foreach ($inputsUpdates as $item) {
                    $parent = $item->getParent();
                    $parent->removeChild($item->id());
                }

                //update button
                $inputUpdate   = new Selector('button[name=update]', new Parser());
                $inputsUpdates = $inputUpdate->find($cartForm);
                foreach ($inputsUpdates as $item) {
                    $parent = $item->getParent();
                    $parent->removeChild($item->id());
                }

                            //disable quantity button
    //                        $quantityButton   = new Selector('.cart__qty-input', new Parser());
    //                        $quantityButtons = $quantityButton->find($cartForm);
    //                        foreach ($quantityButtons as $item) {
    //                            $parent = $item->getParent();
    //                            $item->setAttribute('disabled', 'true');
    //                        }



                $buttons = new Selector('[name=checkout]', new Parser());
                $buttons = $buttons->find($cartForm);
                foreach ($buttons as $button) {
                    $button->removeAttribute('name');
                }

                $buttons = new Selector('[name=goto_pp]', new Parser());
                $buttons = $buttons->find($cartForm);
                foreach ($buttons as $button) {
                    $parent = $button->getParent();
                    $parent->removeChild($button->id());
                }

                $buttons = new Selector('[name=goto_gc]', new Parser());
                $buttons = $buttons->find($cartForm);
                foreach ($buttons as $button) {
                    $parent = $button->getParent();
                    $parent->removeChild($button->id());
                }

                $buttons = new Selector('.amazon-payments-pay-button', new Parser());
                $buttons = $buttons->find($cartForm);
                foreach ($buttons as $button) {
                    $parent = $button->getParent();
                    $parent->removeChild($button->id());
                }

                $buttons = new Selector('.google-wallet-button-holder', new Parser());
                $buttons = $buttons->find($cartForm);
                foreach ($buttons as $button) {
                    $parent = $button->getParent();
                    $parent->removeChild($button->id());
                }

                $buttons = new Selector('.additional-checkout-button', new Parser());
                $buttons = $buttons->find($cartForm);
                foreach ($buttons as $button) {
                    $parent = $button->getParent();
                    $parent->removeChild($button->id());
                }

                $cartForm->setAttribute('action', 'https://checkout.{{ shop.domain }}/');
                $cartForm->setAttribute('id', 'cart_form');
                $cartForm->setAttribute('data-fox', 'cart_form');

                $divFoxScript = new HtmlNode('div');

                $divFoxScript->setAttribute('id', 'foxScript');
                $script = new HtmlNode('script');
                $script->setAttribute('src', 'https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js');
                $divFoxScript->addChild($script);
                $script = new HtmlNode('script');

                $script->addChild(new TextNode("$(document).ready(function (){

                        $(document).on('change', \"input.booster-quantity, input[name^='updates['], input[id^='updates_'], input[id^='Updates_']\", function(e) {
                            e.preventDefault();
                             $('[data-fox=cart_form]').attr('action', '/cart');
                            $('[data-fox=cart_form]').submit();
                          });

                        $('[data-fox=cart_form]').submit(function(){
                            var discount=0;
                            $( '[data-integration-price-saved=1]' ).each(function( key, value ) {
                                if(parseInt(value.innerText.replace(/[^0-9]/g,'')) > discount)
                                {
                                    discount = parseInt(value.innerText.replace(/[^0-9]/g,''))
                                }
                            });
                            $('#cart_form').append(\"<input type='hidden' name='value_discount' value='\"+discount+\"'>\");
                        });

                      });"));

                $divFoxScript->addChild($script);

                $cartForm->addChild($divFoxScript);

                $foxData = "
                        <div id='foxData'>
                        <input type='hidden' data-fox='1' name='product_id_{{ forloop.index }}' value='{{ item.id }}'>
                        <input type='hidden' data-fox='2' name='variant_id_{{ forloop.index }}' value='{{ item.variant_id }}'>
                        <input type='hidden' data-fox='3' name='product_price_{{ forloop.index }}' value='{{ item.price }}'>
                        <input type='hidden' data-fox='4' name='product_image_{{ forloop.index }}' value='{{ item.image }}'>
                        <input type='hidden' data-fox='5' name='product_amount_{{ forloop.index }}' value='{{ item.quantity }}'>
                      </div>";

                $html = $dom->root->outerHtml();
                preg_match_all("/({%)[\s\S]+?(%})/", $html, $tokens, PREG_OFFSET_CAPTURE);
                foreach ($tokens[0] as $key => $item) {
                    if ((stripos($item[0], 'for ') !== false) &&
                        (stripos($item[0], ' in cart.items') !== false)) {
                        $html = substr_replace($html, $foxData, $item[1] + strlen($item[0]), 0);
                    }
                }

                //}

                return $html;
            } else {
                //thown parse error
            }
        }

        public function getCartTemplate($htmlCart)
        {
            $dom = new Dom;
            $dom->load($htmlCart);

            $forms = $dom->find('form');
            foreach ($forms as $form) {
                $data = explode(' ', $form->getAttribute('class'));
                if (in_array('cart', $data)) {
                    $cartForm = $form;
                    break;
                }
            }

            if ($cartForm) {

                //div Foxdata
                $divFoxData = new Selector('#foxData', new Parser());
                $divs       = $divFoxData->find($cartForm);
                foreach ($divs as $div) {
                    $parent = $div->getParent();
                    $parent->removeChild($div->id());
                }

                //div FoxScript
                $divFoxScript = new Selector('#foxScript', new Parser());
                $divs         = $divFoxScript->find($cartForm);
                foreach ($divs as $div) {
                    $parent = $div->getParent();
                    $parent->removeChild($div->id());
                }

                //update button
                $inputUpdate   = new Selector('input[name=update]', new Parser());
                $inputsUpdates = $inputUpdate->find($cartForm);
                foreach ($inputsUpdates as $item) {
                    $parent = $item->getParent();
                    $parent->removeChild($item->id());
                }

                //update button
                $inputUpdate   = new Selector('button[name=update]', new Parser());
                $inputsUpdates = $inputUpdate->find($cartForm);
                foreach ($inputsUpdates as $item) {
                    $parent = $item->getParent();
                    $parent->removeChild($item->id());
                }

                            //disable quantity button
    //                        $quantityButton   = new Selector('.cart__qty-input', new Parser());
    //                        $quantityButtons = $quantityButton->find($cartForm);
    //                        foreach ($quantityButtons as $item) {
    //                            $parent = $item->getParent();
    //                            $item->setAttribute('disabled', 'true');
    //                        }



                $buttons = new Selector('[name=checkout]', new Parser());
                $buttons = $buttons->find($cartForm);
                foreach ($buttons as $button) {
                    $button->removeAttribute('name');
                }

                $buttons = new Selector('[name=goto_pp]', new Parser());
                $buttons = $buttons->find($cartForm);
                foreach ($buttons as $button) {
                    $parent = $button->getParent();
                    $parent->removeChild($button->id());
                }

                $buttons = new Selector('[name=goto_gc]', new Parser());
                $buttons = $buttons->find($cartForm);
                foreach ($buttons as $button) {
                    $parent = $button->getParent();
                    $parent->removeChild($button->id());
                }

                $buttons = new Selector('.amazon-payments-pay-button', new Parser());
                $buttons = $buttons->find($cartForm);
                foreach ($buttons as $button) {
                    $parent = $button->getParent();
                    $parent->removeChild($button->id());
                }

                $buttons = new Selector('.google-wallet-button-holder', new Parser());
                $buttons = $buttons->find($cartForm);
                foreach ($buttons as $button) {
                    $parent = $button->getParent();
                    $parent->removeChild($button->id());
                }

                $buttons = new Selector('.additional-checkout-button', new Parser());
                $buttons = $buttons->find($cartForm);
                foreach ($buttons as $button) {
                    $parent = $button->getParent();
                    $parent->removeChild($button->id());
                }

                $cartForm->setAttribute('action', 'https://checkout.{{ shop.domain }}/');
                $cartForm->setAttribute('id', 'cart_form');
                $cartForm->setAttribute('data-fox', 'cart_form');

                $divFoxScript = new HtmlNode('div');

                $divFoxScript->setAttribute('id', 'foxScript');
                $script = new HtmlNode('script');
                $script->setAttribute('src', 'https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js');
                $divFoxScript->addChild($script);
                $script = new HtmlNode('script');

                $script->addChild(new TextNode("$(document).ready(function (){

                        $(document).on('change', \"input.booster-quantity, input[name^='updates['], input[id^='updates_'], input[id^='Updates_']\", function(e) {
                            e.preventDefault();
                             $('[data-fox=cart_form]').attr('action', '/cart');
                            $('[data-fox=cart_form]').submit();
                          });

                        $('[data-fox=cart_form]').submit(function(){
                            var discount=0;
                            $( '[data-integration-price-saved=1]' ).each(function( key, value ) {
                                if(parseInt(value.innerText.replace(/[^0-9]/g,'')) > discount)
                                {
                                    discount = parseInt(value.innerText.replace(/[^0-9]/g,''))
                                }
                            });
                            $('#cart_form').append(\"<input type='hidden' name='value_discount' value='\"+discount+\"'>\");
                        });

                      });"));

                $divFoxScript->addChild($script);

                $cartForm->addChild($divFoxScript);

                $foxData = "
                        <div id='foxData'>
                        <input type='hidden' data-fox='1' name='product_id_{{ forloop.index }}' value='{{ item.id }}'>
                        <input type='hidden' data-fox='2' name='variant_id_{{ forloop.index }}' value='{{ item.variant_id }}'>
                        <input type='hidden' data-fox='3' name='product_price_{{ forloop.index }}' value='{{ item.price }}'>
                        <input type='hidden' data-fox='4' name='product_image_{{ forloop.index }}' value='{{ item.image }}'>
                        <input type='hidden' data-fox='5' name='product_amount_{{ forloop.index }}' value='{{ item.quantity }}'>
                      </div>";

                $html = $dom->root->outerHtml();
                preg_match_all("/({%)[\s\S]+?(%})/", $html, $tokens, PREG_OFFSET_CAPTURE);
                foreach ($tokens[0] as $key => $item) {
                    if ((stripos($item[0], 'for ') !== false) &&
                        (stripos($item[0], ' in cart.items') !== false)) {
                        $html = substr_replace($html, $foxData, $item[1] + strlen($item[0]), 0);
                    }
                }

                return $html;
            } else {
                //thown parse error
            }
        }
    */
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

