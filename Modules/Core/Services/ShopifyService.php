<?php

namespace Modules\Core\Services;

use App\Entities\Project;
use App\Entities\ShopifyIntegration;
use App\Entities\User;
use Exception;
use App\Entities\Plan;
use Illuminate\Support\Facades\Log;
use Modules\Core\Events\ShopifyIntegrationReadyEvent;
use PHPHtmlParser\Dom;
use App\Entities\Product;
use Slince\Shopify\Client;
use App\Entities\ProductPlan;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;
use PHPHtmlParser\Selector\Parser;
use Vinkla\Hashids\Facades\Hashids;
use PHPHtmlParser\Selector\Selector;
use Slince\Shopify\PublicAppCredential;

class ShopifyService
{
    const templateKeyName     = 'sections/cart-template.liquid';
    const templateAjaxKeyName = 'snippets/ajax-cart-template.liquid';
    /**
     * @var string
     */
    private $cacheDir;
    /**
     * @var PublicAppCredential
     */
    private $credential;
    /**
     * @var Client
     */
    private $client;
    /**
     * @var
     */
    private $theme;

    /**
     * ShopifyService constructor.
     * @param $urlStore
     * @param $token
     */
    public function __construct(string $urlStore, string $token)
    {
        try {
            if (!$this->cacheDir) {
                $cache = '/var/tmp';
            } else {
                $cache = $this->cacheDir;
            }

            $this->credential = new PublicAppCredential($token);
            $this->client     = new Client($this->credential, $urlStore, [
                'metaCacheDir' => $cache // Metadata cache dir, required
            ]);
        } catch (Exception $e) {
            Log::warning('__construct - Erro ao criar servico do shopify');
            report($e);
        }
    }

    /**
     * @param $cacheDir
     * @return $this
     */
    public function cacheDir(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;

        return $this;
    }

    /**
     * @param $cacheDir
     * @return $this
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function themeByRole(string $role)
    {
        $this->theme = $this->getThemeByRole($role);

        return $this;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function themeById(string $role)
    {
        $this->theme = $this->getThemeIdByRole($role);

        return $this;
    }

    /**
     * @param $themeId
     * @return $this
     */
    public function setThemeById($themeId)
    {
        $this->theme = $this->getThemeById($themeId);

        return $this;
    }

    /**
     * @param $role
     * @return bool
     */
    public function setThemeByRole($role)
    {
        $this->theme = $this->getThemeByRole($role);

        if ($this->theme) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \Slince\Shopify\Theme\Theme[]
     */
    public function getAllThemes()
    {
        return $this->client->getThemeManager()->findAll([]);
    }

    /**
     * @param string $role
     * @return mixed
     */
    public function getThemeIdByRole(string $role)
    {
        $theme = $this->getThemeByRole($role);

        return $theme->id;
    }

    /**
     * @return mixed
     */
    public function getThemeName()
    {
        if ($this->theme) {
            return $this->theme->getName();
        } else {
            return ''; //throwl
        }
    }

    /**
     * @param string $role
     * @return \Slince\Shopify\Theme\Theme|null
     */
    public function getThemeByRole(string $role)
    {
        $themes = $this->getAllThemes();

        foreach ($themes as $theme) {
            if ($theme->getRole() == $role)
                return $theme;
        }

        return null; //throwl
    }

    /**
     * @param string $themeId
     * @return \Slince\Shopify\Theme\Theme|null
     */
    public function getThemeById(string $themeId)
    {
        $themes = $this->getAllThemes();

        foreach ($themes as $theme) {
            if ($theme->id == $themeId)
                return $theme;
        }

        return null; //throwl
    }

    /**
     * @return \Slince\Shopify\Manager\Asset\Asset[]|null
     */
    public function getAllTemplates()
    {
        if (!empty($this->theme->id)) {
            return $this->client->getAssetManager()->findAll($this->theme);
        } else {
            return null; //throwl
        }
    }

    /**
     * @param string $templateKeyName
     * @return string|null
     */
    public function getTemplateHtml(string $templateKeyName)
    {
        if (!empty($this->theme)) {
            $templateFiles = $this->client->getAssetManager()->findAll($this->theme->getId());
            foreach ($templateFiles as $file) {
                if ($file->getKey() == $templateKeyName) {
                    $htmlCart = $this->client->getAssetManager()
                                             ->find($this->theme->getId(), $templateKeyName);

                    return $htmlCart->getValue();
                }
            }

            return null;
        } else {
            return null; //throwl
        }
    }

    /**
     * @param string $templateKeyName
     * @param string $value
     * @return bool
     */
    public function setTemplateHtml(string $templateKeyName, string $value)
    {
        if (!empty($this->theme)) {

            $asset = $this->client->getAssetManager()->update($this->theme->getId(), [
                "key"   => $templateKeyName,
                "value" => $value,
            ]);

            if ($asset) {
                return true;
            } else {
                return false;
            }
        } else {
            return false; //throwl
        }
    }

    /**
     * @param string $templateKeyName
     * @param string $value
     * @return bool
     */
    public function updateTemplateHtml(string $templateKeyName, string $value, $domain = null, $ajax = false)
    {
        if (!empty($this->theme)) {
            if ($ajax) {
                $asset = $this->client->getAssetManager()->update($this->theme->getId(), [
                    "key"   => $templateKeyName,
                    "value" => $this->updateCartTemplateAjax($value, $domain),
                ]);
            } else {
                $asset = $this->client->getAssetManager()->update($this->theme->getId(), [
                    "key"   => $templateKeyName,
                    "value" => $this->updateCartTemplate($value, $domain),
                ]);
            }

            if ($asset) {
                return true;
            } else {
                return false;
            }
        } else {
            return false; //throwl
        }
    }

    /**
     * @param string $templateKeyName
     * @param string $value
     * @param bool $ajax
     * @return bool
     * @throws \PHPHtmlParser\Exceptions\CircularException
     */
    public function insertUtmTracking(string $templateKeyName, string $value)
    {
        if (!empty($this->theme)) {

            $asset = $this->client->getAssetManager()->update($this->theme->getId(), [
                "key"   => $templateKeyName,
                "value" => $this->updateThemeTemplate($value),
            ]);

            if ($asset) {
                return true;
            } else {
                return false;
            }
        } else {
            return false; //throwl
        }
    }

    /**
     * @param $html
     * @return string
     * @throws \PHPHtmlParser\Exceptions\CircularException
     */
    public function updateThemeTemplate($html)
    {
        $dom = new Dom;

        $dom->setOptions([
                             'strict'             => false,
                             'preserveLineBreaks' => true,
                             'removeScripts'      => false,
                         ]);

        $dom->load($html);

        $body = $dom->find('body');
        $body = $body[0];

        //div FoxScript
        $foxScriptUtm = new Selector('#foxScriptUtm', new Parser());
        $divs         = $foxScriptUtm->find($body);
        foreach ($divs as $div) {
            $parent = $div->getParent();
            $parent->removeChild($div->id());
        }

        $divFoxScriptUtm = new HtmlNode('div');

        $divFoxScriptUtm->setAttribute('id', 'foxScriptUtm');

        $script = new HtmlNode('script');
        $script->addChild(new TextNode("var url_string = window.location.href;
            var url = new URL(url_string);
            var src = url.searchParams.get('src');
            var utm_source = url.searchParams.get('utm_source');
            var utm_medium = url.searchParams.get('utm_medium');
            var utm_campaign = url.searchParams.get('utm_campaign');
            var utm_term = url.searchParams.get('utm_term');
            var utm_content = url.searchParams.get('utm_content');

            if( (src != null) || (utm_source != null) || (utm_medium != null) || (utm_campaign != null) || (utm_term != null) || (utm_content != null) )
            {
                var cookieName = '_landing_page';
                var cookieValue = 'src='+src+'|'+'utm_source='+utm_source+'|'+'utm_medium='+utm_medium+'|'+'utm_campaign='+utm_campaign+'|'+'utm_term='+utm_term+'|'+'utm_content='+utm_content;
                var myDate = new Date();
                myDate.setMonth(myDate.getMonth() + 12);
                
                document.cookie = cookieName +'=' + cookieValue + ';domain=.{{ shop.domain }};path=/;expires=' + myDate; 
            }")
        );

        $divFoxScriptUtm->addChild($script);
        $body->addChild($divFoxScriptUtm);

        return $dom->root->outerHtml();
    }

    /**
     * @param $htmlCart
     * @return mixed|string
     * @throws \PHPHtmlParser\Exceptions\CircularException
     */
    public function updateCartTemplateAjax($htmlCart, $domain)
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
            /*
                        //disable quantity button
                        $quantityButton   = new Selector('.cart__qty-input', new Parser());
                        $quantityButtons = $quantityButton->find($cartForm);
                        foreach ($quantityButtons as $item) {
                            $parent = $item->getParent();
                            $item->setAttribute('disabled', 'true');
                        }

                        */

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

            $cartForm->setAttribute('action', 'https://checkout.' . $domain . '/');
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

    /**
     * @param $htmlCart
     * @param null $domain
     * @return bool
     */
    public function checkCartTemplate($htmlCart)
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
                return true;
            }

            return false;
        }
    }

    /**
     * @param $htmlCart
     * @return mixed|string
     * @throws \PHPHtmlParser\Exceptions\CircularException
     */
    public function updateCartTemplate($htmlCart, $domain = null)
    {
        preg_match_all("/({%)[\s\S]+?(%})/", $htmlCart, $tokens, PREG_OFFSET_CAPTURE);
        foreach ($tokens[0] as $key => $item) {
            $from     = '/' . preg_quote($item[0], '/') . '/';
            $htmlCart = preg_replace($from, 'fox-fox-fox', $htmlCart, 1);
        }

        $dom = new Dom;
        $dom->setOptions([
                             'strict'             => false, // Set a global option to enable strict html parsing.
                             'preserveLineBreaks' => true,
                         ]);

        $dom->load($htmlCart);
        $html = $dom->root->outerHtml();

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
            /*
                        //disable quantity button
                        $quantityButton   = new Selector('.cart__qty-input', new Parser());
                        $quantityButtons = $quantityButton->find($cartForm);
                        foreach ($quantityButtons as $item) {
                            $parent = $item->getParent();
                            $item->setAttribute('disabled', 'true');
                        }

                        */

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

            $cartForm->setAttribute('action', 'https://checkout.' . $domain . '/');
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
            foreach ($tokens[0] as $key => $item) {
                $from = '/' . preg_quote('fox-fox-fox', '/') . '/';
                $html = preg_replace($from, $item[0], $html, 1);
            }

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
            return '';
        }
    }

    /**
     * @param $projectId
     * @param $userId
     * @param $shopifyProductId
     * @return bool
     */
    public function importShopifyProduct($projectId, $userId, $shopifyProductId)
    {
        $planModel        = new Plan();
        $productModel     = new Product();
        $productPlanModel = new ProductPlan();

        $storeProduct = $this->getShopProduct($shopifyProductId);

        foreach ($storeProduct->getVariants() as $variant) {

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

            $product = $productModel->with('productsPlans')
                            //   ->where('project', $projectId)
                              ->where('shopify_id', $storeProduct->getId())
                              ->where('shopify_variant_id', $variant->getId())
                              ->first();

            if ($product) {
                //plano ja existe, atualiza o plano, produto, produtoplanos

                $product->update([
                                'name'               => substr($storeProduct->getTitle(), 0, 100),
                                'description'        => $description,
                                'weight'             => $variant->getWeight(),
                                'cost'               => $this->getShopInventoryItem($variant->getInventoryItemId())
                                                            ->getCost(),
                                'shopify_id'         => $storeProduct->getId(),
                                'shopify_variant_id' => $variant->getId(),
                ]);

                $productPlan = $productPlanModel->where('product', $product->id)
                                                ->where('amount' , 1)
                                                ->first();

                if($productPlan){

                    $plan = $productModel->find($productPlan->plan);

                    $plan->update([
                        'name'        => substr($storeProduct->getTitle(), 0, 100),
                        'description' => $description,
                        'price'       => $variant->getPrice(),
                        'status'      => '1',
                    ]);

                    Log::warning('plano atualizado');

                    if (count($storeProduct->getVariants()) > 1) {
                        foreach ($storeProduct->getImages() as $image) {

                            foreach ($image->getVariantIds() as $variantId) {
                                if ($variantId == $variant->getId()) {

                                    if ($image->getSrc() != '') {
                                        $product->update([
                                                            'photo' => $image->getSrc(),
                                                        ]);
                                    } else {

                                        $product->update([
                                                            'photo' => $storeProduct->getImage()->getSrc(),
                                                        ]);
                                    }
                                }
                            }
                        }
                    } else {

                        $product->update([
                                            'photo' => $storeProduct->getImage()->getSrc(),
                                        ]);
                    }
                }
                else{
                    $plan = $planModel->create([
                        'shopify_id'         => $storeProduct->getId(),
                        'shopify_variant_id' => $variant->getId(),
                        'project'            => $projectId,
                        'name'               => substr($storeProduct->getTitle(), 0, 100),
                        'description'        => $description,
                        'code'               => '',
                        'price'              => $variant->getPrice(),
                        'status'             => '1',
                    ]);

                    $productPlan = $productPlanModel->create([
                        'product' => $product->id,
                        'plan'    => $plan->id,
                    ]);
                }
            } else {
                //plano nao existe, cria o plano, produto e produtosplanos

                $product = $productModel->create([
                                                     'user'               => $userId,
                                                     'name'               => substr($storeProduct->getTitle(), 0, 100),
                                                     'description'        => $description,
                                                     'guarantee'          => '0',
                                                     'format'             => 1,
                                                     'category'           => '11',
                                                     'cost'               => $this->getShopInventoryItem($variant->getInventoryItemId())
                                                                                  ->getCost(),
                                                     'shopify'            => true,
                                                     'price'              => '',
                                                     'shopify_id'         => $storeProduct->getId(),
                                                     'shopify_variant_id' => $variant->getId(),
                                                 ]);

                $plan = $planModel->create([
                                               'shopify_id'         => $storeProduct->getId(),
                                               'shopify_variant_id' => $variant->getId(),
                                               'project'            => $projectId,
                                               'name'               => substr($storeProduct->getTitle(), 0, 100),
                                               'description'        => $description,
                                               'code'               => '',
                                               'price'              => $variant->getPrice(),
                                               'status'             => '1',
                                           ]);

                $productPlanModel->create([
                                              'product' => $product->id,
                                              'plan'    => $plan->id,
                                              'amount'  => '1',
                                          ]);

                Log::warning('productoplano criado - ' . print_r($productPlanModel, true));

                $plan->update([
                                  'code' => Hashids::encode($plan->id),
                              ]);

                if (count($storeProduct->getVariants()) > 1) {
                    foreach ($storeProduct->getImages() as $image) {

                        foreach ($image->getVariantIds() as $variantId) {
                            if ($variantId == $variant->getId()) {

                                if ($image->getSrc() != '') {
                                    $product->update([
                                                         'photo' => $image->getSrc(),
                                                     ]);
                                } else {

                                    $product->update([
                                                         'photo' => $storeProduct->getImage()->getSrc(),
                                                     ]);
                                }
                            }
                        }
                    }
                } else {

                    $product->update([
                                         'photo' => $storeProduct->getImage()->getSrc(),
                                     ]);
                }
            }
        }

        return true;
    }

    /**
     * @param $projectId
     * @param $userId
     */
    public function importShopifyStore($projectId, $userId)
    {
        $projectModel            = new Project();
        $userModel               = new User();
        $shopifyIntegrationModel = new ShopifyIntegration();

        $shopifyIntegrationModel->where('project', $projectId)->update([
                                                                           'status' => $shopifyIntegrationModel->getEnum('status', 'pending'),
                                                                       ]);

        $storeProducts = $this->getShopProducts();

        foreach ($storeProducts as $shopifyProduct) {
            $this->importShopifyProduct($projectId, $userId, $shopifyProduct->getId());
        }

        $this->createShopifyIntegrationWebhook($projectId, "https://app.cloudfox.net/postback/shopify/");

        $project = $projectModel->find($projectId);
        $user    = $userModel->find($userId);
        if (!empty($project) && !empty($user)) {
            event(new ShopifyIntegrationReadyEvent($user, $project));
            $shopifyIntegrationModel->where('project', $projectId)->update([
                                                                               'status' => $shopifyIntegrationModel->getEnum('status', 'approved'),
                                                                           ]);
        }
    }

    /**
     * @param $projectId
     * @param $url
     * @return bool
     */
    public function createShopifyIntegrationWebhook($projectId, $url)
    {
        $postbackUrl = $url;

        $this->deleteShopWebhook();

        $this->createShopWebhook([
                                     "topic"   => "products/create",
                                     "address" => $postbackUrl . Hashids::encode($projectId),
                                     "format"  => "json",
                                 ]);

        $this->createShopWebhook([
                                     "topic"   => "products/update",
                                     "address" => $postbackUrl . Hashids::encode($projectId),
                                     "format"  => "json",
                                 ]);

        $this->createShopWebhook([
                                     "topic"   => "orders/updated",
                                     "address" => $postbackUrl . Hashids::encode($projectId) . '/tracking',
                                     "format"  => "json",
                                 ]);

        return true;
    }

    /**
     * @return string
     */
    public function getShopName()
    {
        if (!empty($this->client)) {
            return $this->client->getShopManager()
                                ->get()
                                ->getName();
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getShopUrl()
    {
        if (!empty($this->client)) {
            return 'https://' . $this->client->getShopManager()
                                             ->get()
                                             ->getDomain();
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getShopDomain()
    {
        if (!empty($this->client)) {
            return $this->client->getShopManager()
                                ->get()
                                ->getDomain();
        } else {
            return '';
        }
    }

    /**
     * @return int|string
     */
    public function getShopId()
    {
        if (!empty($this->client)) {
            return $this->client->getShopManager()
                                ->get()
                                ->getId();
        } else {
            return '';
        }
    }

    /**
     * @return array|\Slince\Shopify\Manager\Product\Product[]
     */
    public function getShopProducts()
    {
        if (!empty($this->client)) {
            return $this->client->getProductManager()
                                ->findAll([]);
        } else {
            return [];
        }
    }

    /**
     * @param $variantId
     * @return array|\Slince\Shopify\Manager\Product\Product
     */
    public function getShopProduct($variantId)
    {
        if (!empty($this->client)) {
            return $this->client->getProductManager()
                                ->find($variantId);
        } else {
            return [];
        }
    }

    /**
     * @param $shopifyItemId
     * @return array|\Slince\Shopify\Manager\InventoryItem\InventoryItem
     */
    public function getShopInventoryItem($shopifyItemId)
    {
        if (!empty($this->client)) {
            return $this->client->getInventoryItemManager()
                                ->find($shopifyItemId);
        } else {
            return [];
        }
    }

    /**
     * @param array $data
     * @return \Slince\Shopify\Manager\Webhook\Webhook|null
     */
    public function createShopWebhook($data = [])
    {
        if (!empty($this->client)) {
            return $this->client->getWebhookManager()->create($data);
        } else {
            return null;
        }
    }

    /**
     * @param null $webhookId
     * @return array|\Slince\Shopify\Manager\Webhook\Webhook|\Slince\Shopify\Manager\Webhook\Webhook[]
     */
    public function getShopWebhook($webhookId = null)
    {
        if (!empty($this->client)) {
            if ($webhookId) {
                return $this->client->getWebhookManager()->find($webhookId);
            } else {
                return $this->client->getWebhookManager()->findAll();
            }
        } else {
            return [];
        }
    }

    /**
     * @param null $webhookId
     * @return array|bool|\Slince\Shopify\Manager\Webhook\Webhook[]
     */
    public function deleteShopWebhook($webhookId = null)
    {
        if (!empty($this->client)) {
            if ($webhookId) {
                return $this->client->getWebhookManager()->remove($webhookId);
            } else {
                $webhooks = $this->getShopWebhook();
                foreach ($webhooks as $webhook) {
                    $this->client->getWebhookManager()->remove($webhook->getId());
                }

                return $this->client->getWebhookManager()->findAll();
            }
        } else {
            return [];
        }
    }

    /**
     * @param null $variantId
     * @return Slince\Shopify\Manager\ProductVariant\Variant|null
     */
    public function getProductVariant($variantId = null)
    {
        if (!empty($this->client)) {
            if ($variantId) {
                return $this->client->getProductVariantManager()->find($variantId);
            }
        } else {
            return null;
        }
    }

    /**
     * @param null $productId
     * @return Slince\Shopify\Manager\Product\Product|null
     */
    public function getProduct($productId = null)
    {
        if (!empty($this->client)) {
            if ($productId) {
                return $this->client->getProductManager()->find($productId);
            }
        } else {
            return null;
        }
    }

    /**
     * @param null $imageId
     * @return Slince\Shopify\Manager\ProductImage\Image|null
     */
    public function getImage($productId = null, $imageId = null)
    {
        if (!empty($this->client)) {
            if ($productId && $imageId) {
                return $this->client->getProductImageManager()->find($productId, $imageId);
            }
        } else {
            return null;
        }
    }
}


