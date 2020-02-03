<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\FoxUtils;
use PHPHtmlParser\Dom;
use Slince\Shopify\Client;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\User;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Project;
use PHPHtmlParser\Selector\Parser;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use PHPHtmlParser\Selector\Selector;
use Modules\Core\Entities\ProductPlan;
use Slince\Shopify\Manager\Asset\Asset;
use Slince\Shopify\Manager\Theme\Theme;
use Slince\Shopify\PublicAppCredential;
use PHPHtmlParser\Exceptions\CurlException;
use Slince\Shopify\Manager\Webhook\Webhook;
use Modules\Core\Entities\SaleShopifyRequest;
use Modules\Core\Entities\ShopifyIntegration;
use PHPHtmlParser\Exceptions\StrictException;
use Slince\Shopify\Manager\ProductImage\Image;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use Slince\Shopify\Manager\ProductVariant\Variant;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use Modules\Core\Events\ShopifyIntegrationReadyEvent;
use Laracasts\Presenter\Exceptions\PresenterException;
use PHPHtmlParser\Exceptions\UnknownChildTypeException;
use Slince\Shopify\Manager\InventoryItem\InventoryItem;

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
     * @var int
     */
    private $saleId;
    /**
     * @var array
     */
    private $sendData = [];
    /**
     * @var array
     */
    private $receivedData = [];
    /**
     * @var array
     */
    private $exceptions = [];
    /**
     * @var string
     */
    private $method;
    /**
     * @var string
     */
    private $project = "admin";
    /**
     * @var bool
     */
    private $skipToCart = false;

    /**
     * ShopifyService constructor.
     * @param string $urlStore
     * @param string $token
     */
    public function __construct(string $urlStore, string $token)
    {
        if (!$this->cacheDir) {
            $cache = '/var/tmp';
        } else {
            $cache = $this->cacheDir;
        }

        $this->credential = new PublicAppCredential($token);
        $this->client     = new Client($this->credential, $urlStore, [
            'metaCacheDir' => $cache // Metadata cache dir, required
        ]);

        $this->getAllThemes();
    }

    /**
     * @param string $cacheDir
     * @return $this
     */
    public function cacheDir(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;

        return $this;
    }

    /**
     * @return Client
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
     * @return \Slince\Shopify\Theme\Theme
     */
    public function getThemeByRole(string $role)
    {
        $themes = $this->getAllThemes();

        foreach ($themes as $theme) {
            if ($theme->getRole() == $role)
                return $theme;
        }

        return null;
    }

    /**
     * @param string $themeId
     * @return Theme|null
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
     * @return Asset[]|null
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
     * @param null $domain
     * @param bool $ajax
     * @param bool $skipToCart
     * @return bool
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws CurlException
     * @throws NotLoadedException
     * @throws StrictException
     * @throws UnknownChildTypeException
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
     * @throws CircularException
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
     * @throws CircularException
     */
    public function updateThemeTemplate($html)
    {

        $startScriptPos = strpos($html, "<!-- start cloudfox utm script -->");
        $endScriptPos   = strpos($html, "<!-- end cloudfox utm script -->");

        if ($startScriptPos !== false) {
            //script já existe, remove
            $size = ($endScriptPos + 32) - $startScriptPos;

            $html = substr_replace($html, '', $startScriptPos, $size);
        }

        $strPos = strpos($html, '</body>');

        $scriptFox = "<!-- start cloudfox utm script -->
        <div id='foxScriptUtm'>
        <script>

            var url_string = window.location.href;
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
            }

        </script>
        </div>
        <!-- end cloudfox utm script -->";

        $html = substr_replace($html, $scriptFox, $strPos, 0);

        return $html;
    }

    /**
     * @param $htmlCart
     * @param $domain
     * @return string|string[]|null
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws CurlException
     * @throws NotLoadedException
     * @throws StrictException
     * @throws UnknownChildTypeException
     */
    public function updateCartTemplateAjax($htmlCart, $domain)
    {
        preg_match_all("/({%)[\s\S]+?(%})/", $htmlCart, $tokens, PREG_OFFSET_CAPTURE);
        foreach ($tokens[0] as $key => $item) {
            $from     = '/' . preg_quote($item[0], '/') . '/';
            $htmlCart = preg_replace($from, 'fox-fox-fox', $htmlCart, 1);
        }

        preg_match_all("/({{)[\s\S]+?(}})/", $htmlCart, $tokens2, PREG_OFFSET_CAPTURE);
        foreach ($tokens2[0] as $key => $item) {
            $from     = '/' . preg_quote($item[0], '/') . '/';
            $htmlCart = preg_replace($from, 'fox1-fox1-fox1', $htmlCart, 1);
        }

        $dom = new Dom;
        $dom->setOptions([
                             'strict'             => false, // Set a global option to enable strict html parsing.
                             'preserveLineBreaks' => true,
                             'removeScripts'      => false,
                         ]);

        $dom->load($htmlCart);

        $forms = $dom->find('form');
        foreach ($forms as $form) {
            $data = explode(' ', $form->getAttribute('class'));
            if (in_array('ajaxcart', $data)) {
                $cartForm = $form;
                break;
            }
        }

        if ($cartForm ?? null) {

            $inputUpdate   = new Selector('button[name=checkout]', new Parser());
            $inputsUpdates = $inputUpdate->find($cartForm);
            foreach ($inputsUpdates as $item) {
                $item->removeAttribute('name');

                $buttonClass = $item->getAttribute('class');
                $buttonClass = str_replace("cart__checkout", "", $buttonClass);
                $item->setAttribute('class', $buttonClass);

                $item->setAttribute('type', 'button');
                $item->setAttribute('onclick', 'foxCheckout();');
            }

            //div FoxScript
            $divFoxScript = new Selector('#foxScript', new Parser());
            $divs         = $divFoxScript->find($cartForm);
            foreach ($divs as $div) {
                $parent = $div->getParent();
                $parent->removeChild($div->id());
            }

            $divFoxScript = new HtmlNode('div');
            $divFoxScript->setAttribute('id', 'foxScript');
            $script = new HtmlNode('script');

            $script->addChild(new TextNode("function foxCheckout()
      {
        $.ajax({
            method: 'GET',
            url: '/cart.js',
            dataType: 'json',
            error: function error(response) {

            },
            success: function success(response) {
              var form = document.createElement('form');
              form.method = 'POST';
              form.action = 'https://checkout." . $domain . "/';

              for(x=0;x < response.items.length;x++)
              {
                var product_id = document.createElement('input');
                var variant_id = document.createElement('input');
                var product_price = document.createElement('input');
                var product_image = document.createElement('input');
                var product_amount = document.createElement('input');

                product_id.name = 'product_id_' + (parseInt(x) + parseInt(1));
                variant_id.name = 'variant_id_' + (parseInt(x) + parseInt(1));
                product_price.name = 'product_price_' + (parseInt(x) + parseInt(1));
                product_image.name = 'product_image_' + (parseInt(x) + parseInt(1));
                product_amount.name = 'product_amount_' + (parseInt(x) + parseInt(1));

                product_id.value = response.items[x].id;
                variant_id.value = response.items[x].variant_id;
                product_price.value = response.items[x].price;
                product_image.value = response.items[x].image;
                product_amount.value = response.items[x].quantity;

                form.appendChild(product_id);
                form.appendChild(variant_id);
                form.appendChild(product_price);
                form.appendChild(product_image);
                form.appendChild(product_amount);
              }

    	      document.body.appendChild(form);

    		  form.submit();
            }
        });
      }"));

            $divFoxScript->addChild($script);
            $cartForm->addChild($divFoxScript);

            $html = $dom->root->outerHtml();

            foreach ($tokens2[0] as $key => $item) {
                $from = '/' . preg_quote('fox1-fox1-fox1', '/') . '/';
                $html = preg_replace($from, $item[0], $html, 1);
            }

            foreach ($tokens[0] as $key => $item) {
                $from = '/' . preg_quote('fox-fox-fox', '/') . '/';
                $html = preg_replace($from, $item[0], $html, 1);
            }

            return $html;
        } else {
            //thown parse error
            return '';
        }
    }

    /**
     * @param $htmlCart
     * @return bool
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws CurlException
     * @throws NotLoadedException
     * @throws StrictException
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

        if ($cartForm ?? null) {
            //div Foxdata
            $divFoxData = new Selector('#foxData', new Parser());
            $divs       = $divFoxData->find($cartForm);
            foreach ($divs as $div) {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * @param $htmlCart
     * @param null $domain
     * @param bool $skipToCart
     * @return mixed|string|string[]|null
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws CurlException
     * @throws NotLoadedException
     * @throws StrictException
     * @throws UnknownChildTypeException
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

        if ($cartForm ?? null) {

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

            if ($this->skipToCart) {
                $skipScript = new HtmlNode('script');
                $skipScript->addChild(new TextNode("if(document.cookie.match(new RegExp('cart=([^;]+)'))){
                                                             document.getElementsByTagName('body').item(0).style.display = 'none';
                                                             let htmlData = `<div><style>@keyframes loader-circle{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}.loader-container{height:100vh;text-align:center;padding-top:40vh}.loader{width:75px;height:75px;display:inline-block;border-top:solid #d3d3d3;border-right:solid #d3d3d3;border-bottom:solid #d3d3d3;border-left:solid #557b96;border-width:5px;border-radius:50%;animation:loader-circle 1.1s infinite linear}</style><div class='loader-container'><div class='loader'></div></div></div>`;
                                                             document.getElementsByTagName('html').item(0).insertAdjacentHTML( 'beforeend', htmlData);
                                                             document.querySelector('.cart__submit, .checkout_btn').click();
                                                             document.cookie = 'cart=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
                                                          }"));
                $divFoxScript->addChild($skipScript);
            }

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

        $planModel = new Plan();

        $productModel = new Product();

        $productPlanModel = new ProductPlan();

        $storeProduct = $this->getShopProduct($shopifyProductId);

        if (empty($storeProduct)) {
            return false;
        }

        foreach ($storeProduct->getVariants() as $variant) {
            $title       = '';
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
                if (empty($storeProduct->getTitle())) {
                    $title = 'Produto sem nome';
                } else {
                    $title = mb_substr($storeProduct->getTitle(), 0, 100);
                }
            } catch (Exception $e) {
                //
            }
            $product = $productModel->with('productsPlans')
                                    ->where('shopify_id', $storeProduct->getId())
                                    ->where('shopify_variant_id', $variant->getId())
                                    ->where('project_id', $projectId)
                                    ->first();
            if ($product) {

                $product->update(
                    [
                        'name'               => $title,
                        'description'        => mb_substr($description, 0, 100),
                        'weight'             => $variant->getWeight(),
                        //'cost'               => $this->getShopInventoryItem($variant->getInventoryItemId())->getCost(),
                        'shopify_id'         => $storeProduct->getId(),
                        'shopify_variant_id' => $variant->getId(),
                        'sku'                => $variant->getSku(),
                        'project_id'         => $projectId,
                    ]
                );

                $productPlan = $productPlanModel->where('product_id', $product->id)
                                                ->where('amount', 1)
                                                ->orderBy('id', 'ASC')
                                                ->first();
                if (!empty($productPlan)) {

                    $plan = $planModel->find($productPlan->plan_id);
                    $plan->update(
                        [
                            'name'        => $title,
                            'description' => mb_substr($description, 0, 100),
                            'price'       => $variant->getPrice(),
                            'status'      => '1',
                            'project_id'  => $projectId,
                        ]
                    );

                    $photo = '';
                    if (count($storeProduct->getVariants()) > 1) {
                        foreach ($storeProduct->getImages() as $image) {
                            $variantIds = $image->getVariantIds();
                            foreach ($variantIds as $variantId) {
                                if ($variantId == $variant->getId()) {
                                    if ($image->getSrc() != '') {
                                        $photo = $image->getSrc();
                                    } else {
                                        $photo = $storeProduct->getImage()->getSrc();
                                    }
                                }
                            }
                        }
                    }
                    if (empty($photo)) {
                        $image = $storeProduct->getImage();
                        if (!empty($image)) {
                            try {
                                $photo = $image->getSrc();
                            } catch (Exception $e) {
                                Log::warning('Erro ao importar foto do shopify');
                                report($e);
                            }
                        }
                    }
                    $product->update(['photo' => $photo]);
                } else {
                    $plan = $planModel->create(
                        [
                            'shopify_id'         => $storeProduct->getId(),
                            'shopify_variant_id' => $variant->getId(),
                            'project_id'         => $projectId,
                            'name'               => $title,
                            'description'        => mb_substr($description, 0, 100),
                            'code'               => '',
                            'price'              => $variant->getPrice(),
                            'status'             => '1',
                        ]
                    );

                    $productPlanModel->create(
                        [
                            'product_id' => $product->id,
                            'plan_id'    => $plan->id,
                            'amount'     => 1,
                        ]
                    );
                    $plan->update(['code' => Hashids::encode($plan->id)]);
                }
            } else {

                $product = $productModel->create(
                    [
                        'user_id'            => $userId,
                        'name'               => $title,
                        'description'        => mb_substr($description, 0, 100),
                        'guarantee'          => '0',
                        'format'             => 1,
                        'category_id'        => '11',
                        'cost'               => $this->getShopInventoryItem($variant->getInventoryItemId())->getCost(),
                        'shopify'            => true,
                        'price'              => '',
                        'shopify_id'         => $storeProduct->getId(),
                        'shopify_variant_id' => $variant->getId(),
                        'sku'                => $variant->getSku(),
                        'project_id'         => $projectId,
                    ]
                );

                $plan = $planModel->create(
                    [
                        'shopify_id'         => $storeProduct->getId(),
                        'shopify_variant_id' => $variant->getId(),
                        'project_id'         => $projectId,
                        'name'               => $title,
                        'description'        => mb_substr($description, 0, 100),
                        'code'               => '',
                        'price'              => $variant->getPrice(),
                        'status'             => '1',
                    ]
                );
                $plan->update(['code' => Hashids::encode($plan->id)]);
                $productPlanModel->create(
                    [
                        'product_id' => $product->id,
                        'plan_id'    => $plan->id,
                        'amount'     => '1',
                    ]
                );
                $photo = '';
                if (count($storeProduct->getVariants()) > 1) {
                    foreach ($storeProduct->getImages() as $image) {
                        $variantIds = $image->getVariantIds();
                        foreach ($variantIds as $variantId) {
                            if ($variantId == $variant->getId()) {
                                if ($image->getSrc() != '') {
                                    $photo = $image->getSrc();
                                } else {
                                    $photo = $storeProduct->getImage()->getSrc();
                                }
                            }
                        }
                    }
                }
                if (empty($photo)) {
                    $image = $storeProduct->getImage();
                    if (!empty($image)) {
                        try {
                            $photo = $image->getSrc();
                        } catch (Exception $e) {
                            Log::warning('Erro ao importar foto do shopify');
                            report($e);
                        }
                    }
                    //$photo = $storeProduct->getImage()->getSrc();
                }
                $product->update(['photo' => $photo]);
            }
        }

        return true;
    }

    /**
     * @param $projectId
     * @param $userId
     * @throws PresenterException
     */
    public function importShopifyStore($projectId, $userId)
    {
        $projectModel = new Project();

        $userModel = new User();

        $shopifyIntegrationModel = new ShopifyIntegration();
        $shopifyIntegrationModel->where('project_id', $projectId)
                                ->update(
                                    [
                                        'status' => $shopifyIntegrationModel->present()->getStatus('pending'),
                                    ]
                                );

        $storeProducts = $this->getShopProducts();

        $page = 1;
        while (!empty($storeProducts)) {

            $i = 0;
            foreach ($storeProducts as $shopifyProduct) {
                try {
                    $i = $i + 1;
                    $this->importShopifyProduct($projectId, $userId, $shopifyProduct->getId());
                } catch (Exception $e) {
                    Log::warning('Erro ao importar produto do shopify');
                    report($e);
                }
            }

            $page          += 1;
            $storeProducts = $this->getShopProducts($page);
        }

        $this->createShopifyIntegrationWebhook($projectId, "https://app.cloudfox.net/postback/shopify/");

        /** @var Project $project */
        $project = $projectModel->find($projectId);
        /** @var User $user */
        $user = $userModel->find($userId);
        if (!empty($project) && !empty($user)) {
            event(new ShopifyIntegrationReadyEvent($user, $project));
            $shopifyIntegrationModel->where('project_id', $projectId)
                                    ->update(
                                        [
                                            'status' => $shopifyIntegrationModel->present()->getStatus('approved'),
                                        ]
                                    );
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
    public function getShopProducts($page = null)
    {
        if (!empty($this->client)) {
            if ($page) {
                $filter = [
                    'page'  => $page,
                    'limit' => 250,
                ];
            } else {
                $filter = [
                    'limit' => 250,
                ];
            }

            //            $x = $this->client->getProductManager()
            //                              ->count();

            return $this->client->getProductManager()
                                ->findAll($filter);
        } else {
            return [];
        }
    }

    /**
     * @param $variantId
     * @return \Slince\Shopify\Manager\Product\Product
     */
    public function getShopProduct($variantId)
    {
        if (!empty($this->client)) {
            return $this->client->getProductManager()
                                ->find($variantId);
        } else {
            return null;
        }
    }

    /**
     * @param $shopifyItemId
     * @return array|InventoryItem
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
     * @return Webhook|null
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
     * @return array|Webhook|Webhook[]
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
     * @return array|bool|Webhook[]
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
     * @return Variant|null
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
     * @return \Slince\Shopify\Manager\Product\Product|null
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
     * @param null $productId
     * @param null $imageId
     * @return Image|null
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

    /**
     * @param Sale $sale
     * @return array
     */
    public function newOrder(Sale $sale)
    {
        try {
            $this->method = __METHOD__;
            $this->saleId = $sale->id;
            $delivery     = $sale->delivery;
            $client       = $sale->customer;
            $checkout     = $sale->checkout;

            $totalValue = $sale->present()->getSubTotal();

            $firstProduct = true;
            $items        = [];
            foreach ($sale->plansSales as $planSale) {

                foreach ($planSale->plan->productsPlans as $productPlan) {

                    $productPrice = 0;
                    if ($firstProduct) {
                        if (!empty($sale->shopify_discount)) {
                            $totalValue -= preg_replace("/[^0-9]/", "", $sale->shopify_discount);
                        }

                        if ($productPlan->amount * $planSale->amount > 1) {
                            $productPrice = intval($totalValue / ($productPlan->amount * $planSale->amount));
                        } else {
                            $productPrice = $totalValue;
                        }
                        $productPrice = substr_replace($productPrice, '.', strlen($productPrice) - 2, 0);
                        $firstProduct = false;
                    }

                    $items[] = [
                        "grams"             => 500,
                        "id"                => $planSale->plan->id,
                        "price"             => $productPrice,
                        "product_id"        => $productPlan->product->shopify_id,
                        "quantity"          => $productPlan->amount * $planSale->amount,
                        "requires_shipping" => true,
                        "sku"               => $productPlan->product->sku,
                        "title"             => $productPlan->product->name,
                        "variant_id"        => $productPlan->product->shopify_variant_id,
                        "variant_title"     => $productPlan->product->name,
                        "name"              => $productPlan->product->name,
                        "gift_card"         => false,
                    ];
                }
            }

            $address = $delivery->street . ' - ' . $delivery->number;
            if ($delivery->complement != '') {
                $address .= ' - ' . $delivery->complement;
            }
            $address .= ' - ' . $delivery->neighborhood;

            $shippingAddress = [
                "address1"      => $address,
                //                "address2"      => "(" . FoxUtilsService::formatDocument($client->document) . ")",
                "address2"      => "",
                "city"          => $delivery->city,
                "company"       => $client->document,
                "country"       => "Brasil",
                "first_name"    => $delivery->present()->getReceiverFirstName(),
                "last_name"     => $delivery->present()->getReceiverLastName(),
                "phone"         => $client->telephone,
                "province"      => $delivery->state,
                "zip"           => FoxUtils::formatCEP($delivery->zip_code),
                "name"          => $client->name,
                "country_code"  => "BR",
                "province_code" => $delivery->state,
            ];
            $shippingValue   = intval(preg_replace("/[^0-9]/", "", $sale->shipment_value));
            if ($shippingValue <= 0) {
                $shippingTitle = 'Free Shipping';
            } else {
                $shippingTitle = 'Standard Shipping';
                $totalValue    += $shippingValue;
            }
            $shipping[] = [
                "custom" => true,
                "price"  => $shippingValue <= 0 ? 0.0 : substr_replace($shippingValue, '.', strlen($shippingValue) - 2, 0),
                "title"  => $shippingTitle,
            ];
            $orderData  = [
                "accepts_marketing"       => false,
                "currency"                => "BRL",
                "email"                   => $client->email,
                "phone"                   => $client->telephone,
                "first_name"              => $delivery->present()->getReceiverFirstName(),
                "last_name"               => $delivery->present()->getReceiverLastName(),
                "buyer_accepts_marketing" => false,
                "line_items"              => $items,
                "shipping_address"        => $shippingAddress,
                "shipping_lines"          => $shipping,
                "note_attributes"         => [
                    "token_cloudfox" => Hashids::encode($sale->checkout_id),
                ],
                "total_price"             => substr_replace($totalValue, '.', strlen($totalValue) - 2, 0),
            ];

            if (($sale->payment_method == 1 || $sale->payment_method == 3) && $sale->status == 1) {
                //cartao aprovado

                $orderData += [
                    "transactions" => [
                        [
                            "kind"   => "sale",
                            "status" => "success",
                            "amount" => substr_replace($totalValue, '.', strlen($totalValue) - 2, 0),
                        ],
                    ],
                ];
            } else if (($sale->payment_method == 2) && $sale->status == 2) {
                //boleto pending

                $orderData += [
                    "financial_status" => "pending",
                    //                    "transactions"     => [
                    //                        [
                    //                            "kind"    => "Boleto",
                    //                            "gateway" => "Boleto",
                    //                            "status"  => "success",
                    //                            "amount"  => substr_replace($totalValue, '.', strlen($totalValue) - 2, 0),
                    //                        ],
                    //                    ],
                ];
            } else if (($sale->payment_method == 2) && $sale->status == 1) {
                //boleto pago

                $orderData += [
                    "transactions" => [
                        [
                            "kind"   => "sale",
                            "status" => "success",
                            "amount" => substr_replace($totalValue, '.', strlen($totalValue) - 2, 0),
                        ],
                    ],
                ];
            } else {
                return [
                    'status'  => 'error',
                    'message' => 'Venda não atende requisitos para gerar ordem no shopify.',
                ];
            }

            $this->sendData = $orderData;
            //            $order              = $this->client->getOrderManager()->create($orderData);

            $order = $this->client->post('orders', [
                'order' => $orderData,
            ]);
            //            $this->receivedData = $this->convertToArray($order);
            $this->receivedData = $order;

            if (FoxUtils::isEmpty($order['order']['id'])) {
                return [
                    'status'  => 'error',
                    'message' => 'Error ao tentar gerar ordem no shopify.',
                ];
            }
            $sale->update([
                              'shopify_order' => $order['order']['id'],
                          ]);

            return [
                'status'  => 'success',
                'message' => 'Ordem gerada com sucesso.',
            ];
        } catch (Exception $e) {
            $this->exceptions[] = $e->getMessage();
            Log::emergency('erro ao criar uma ordem pendente no shopify com a venda ' . $sale->id . ', gerando com com telefone coringa...');
            report($e);

            $shippingAddress['phone']      = '+5555959844325';
            $orderData['phone']            = '+5555959844325';
            $orderData['shipping_address'] = $shippingAddress;

            $this->sendData = $orderData;
            //            $order              = $this->client->getOrderManager()->create($orderData);

            $order = $this->client->post('orders', [
                'order' => $orderData,
            ]);
            //            $this->receivedData = $this->convertToArray($order);
            $this->receivedData = $order;

            if (FoxUtils::isEmpty($order['order']['id'])) {
                return [
                    'status'  => 'error',
                    'message' => 'Error ao tentar gerar ordem no shopify.',
                ];
            }
            $sale->update([
                              'shopify_order' => $order['order']['id'],
                          ]);

            return [
                'status'  => 'success',
                'message' => 'Ordem gerada com sucesso.',
            ];
        }
    }

    /**
     * @param $sale
     * @throws Exception
     */
    public function refundOrder($sale)
    {
        try {
            $this->method = __METHOD__;
            $this->saleId = $sale->id;
            //            $credential   = new PublicAppCredential($shopifyIntegration->token);
            //
            //            $client = new Client($credential, $shopifyIntegration->url_store, [
            //                'metaCacheDir' => '/var/tmp',
            //            ]);
            //            $order = $client->getOrderManager()->find($sale->shopify_order);

            $order = $this->client->get('orders/' . $sale->shopify_order);
            if (!FoxUtils::isEmpty($order)) {
                if ($order['order']['financial_status'] == 'pending') {
                    $data               = $sale->shopify_order;
                    $this->sendData     = $data;
                    $result             = $this->client->getOrderManager()->cancel($data);
                    $this->receivedData = $this->convertToArray($result);
                    // caso getOrderManager->cancel da error, trocar por esse( porem esse deleta a ordem, não cancela)
                    //                    $result = $this->client->delete('orders/' . $order['order']['id']);
                    //                    $this->receivedData = $result;
                } else {
                    $transaction        = [
                        "kind"   => "refund",
                        "source" => "external",
                        "amount" => "",
                    ];
                    $this->sendData     = $transaction;
                    $result             = $this->client->getTransactionManager()
                                                       ->create($sale->shopify_order, $transaction);
                    $this->receivedData = $this->convertToArray($result);
                }
            } else {
                throw new Exception('Ordem não encontrado no shopify para a venda - ' . $order);
            }
        } catch (Exception $ex) {
            $this->exceptions[] = $ex->getMessage();
            throw $ex;
        }
    }

    /**
     * @param $object
     * @return array
     * @author Fausto Marins
     */
    public function convertToArray($object)
    {
        try {
            $result = [];
            foreach ((object) (array) $object as $key => $value) {
                if (is_string($value) || is_null($value)) {
                    $result[$key] = $value;
                } else if (is_array($value)) {
                    $sub = [];
                    foreach ($value as $arrayKey => $arrayValue) {
                        foreach ((object) (array) $arrayValue as $k => $v) {
                            $sub[$arrayKey][$k] = $v;
                        }
                    }
                    $result[$key] = $sub;
                } else {
                    $sub = [];
                    foreach ((object) (array) $value as $k => $v) {
                        $sub[$k] = $v;
                    }
                    $result[$key] = $sub;
                }
            }

            return $result;
        } catch (Exception $ex) {
            $this->exceptions[] = $ex->getMessage();
            report($ex);

            return [];
        }
    }

    /**
     * @return int
     */
    private function getSaleId()
    {
        return $this->saleId;
    }

    /**
     * @return array
     */
    private function getSendData()
    {
        return json_encode($this->sendData ?? []);
    }

    /**
     * @return array
     */
    private function getReceivedData()
    {
        return json_encode($this->receivedData ?? []);
    }

    /**
     * @return false|string|null
     */
    private function getExceptions()
    {
        $exceptions = $this->exceptions ?? [];
        if (FoxUtils::isEmpty($exceptions)) {
            return null;
        } else {
            return json_encode($exceptions);
        }
    }

    /**
     * @return string
     */
    private function getProject()
    {
        return $this->project;
    }

    /**
     * @return string
     */
    private function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array
     */
    private function getAllData()
    {
        return [
            "project"       => $this->getProject(),
            "method"        => $this->getMethod(),
            "sale_id"       => $this->getSaleId(),
            "send_data"     => $this->getSendData(),
            "received_data" => $this->getReceivedData(),
            "exceptions"    => $this->getExceptions(),
        ];
    }

    /**
     * @return void
     */
    public function saveSaleShopifyRequest()
    {
        try {
            SaleShopifyRequest::create($this->getAllData());

            return;
        } catch (Exception $ex) {
            report($ex);

            return;
        }
    }

    /**
     * @return boolean
     * Ensure if the token entered at integration
     * creation has the required permissions
     */
    public function verifyPermissions()
    {
        $permissions = $this->testOrdersPermissions();

        if ($permissions['status'] == 'error') {
            return $permissions;
        }

        $permissions = $this->testProductsPermissions();

        if ($permissions['status'] == 'error') {
            return $permissions;
        }
        $permissions = $this->testThemePermissions();

        if ($permissions['status'] == 'error') {
            return $permissions;
        }

        return $permissions;
    }

    /**
     * @return boolean
     * Verify if the informed token has permission to manage orders on shopify
     */
    public function testOrdersPermissions()
    {

        try {
            $items = [];

            $items[] = [
                "grams"             => 500,
                "id"                => 100,
                "price"             => 100.00,
                "product_id"        => 1000,
                "quantity"          => 1,
                "requires_shipping" => true,
                "sku"               => 1234566789,
                "title"             => 'Cloudfox Test',
                "variant_id"        => 20000,
                "variant_title"     => 'Cloudfox Test',
                "name"              => 'Cloudfox Test',
                "gift_card"         => false,
            ];

            $shippingAddress = [
                "address1"      => "Rio Grande do Sul - RS",
                "address2"      => "",
                "city"          => "Porto Alegre",
                "company"       => "25800004021",
                "country"       => "Brasil",
                "first_name"    => 'Cloud',
                "last_name"     => 'Fox',
                "phone"         => '+5524999999999',
                "province"      => 'RS',
                "zip"           => '',
                "name"          => 'Cloudfox',
                "country_code"  => "BR",
                "province_code" => '',
            ];

            $orderData = [
                "accepts_marketing"       => false,
                "currency"                => "BRL",
                "email"                   => 'test@cloudfox.net',
                "phone"                   => '+5524999999999',
                "first_name"              => 'Cloud',
                "last_name"               => 'Fox',
                "buyer_accepts_marketing" => false,
                "line_items"              => $items,
                "shipping_address"        => $shippingAddress,

            ];

            $orderData += [
                "transactions" => [
                    [
                        "kind"   => "sale",
                        "status" => "success",
                        "amount" => 100.00,
                    ],
                ],
            ];

            // $order = $this->client->getOrderManager()->create($orderData);
            $order = $this->client->post('orders', [
                'order' => $orderData,
            ]);

            // dd($order);

            // if (empty($order) || empty($order->getId())) {
            if (empty($order) || empty($order['order']['id'])) {
                return [
                    'status'  => 'error',
                    'message' => 'Erro na permissão de pedidos',
                ];
            }

            // $this->client->getOrderManager()->remove($order->getId());
            $this->client->delete('orders/' . $order['order']['id']);

            return [
                'status' => 'success',
            ];
        } catch (Exception $e) {
            report($e);

            return [
                'status'  => 'error',
                'message' => 'Erro na permissão de pedidos',
            ];
        }
    }

    /**
     * @return boolean
     * Verify if the informed token has permission to manage products on shopify
     */
    public function testProductsPermissions()
    {

        try {
            $products = $this->client->getProductManager()->findAll();

            if (empty($products)) {
                return [
                    'status'  => 'error',
                    'message' => 'Erro na permissão de produtos',
                ];
            }

            foreach ($products as $product) {

                foreach ($product->getVariants() as $variant) {
                    $productCost = $this->getShopInventoryItem($variant->getInventoryItemId())->getCost();
                    break;
                }

                return [
                    'status' => 'success',
                ];
            }
        } catch (Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Erro na permissão de produtos',
            ];
        }
    }

    /**
     * @return boolean
     * Verify if the informed token has permission to edit theme assets on shopify
     */
    public function testThemePermissions()
    {

        try {

            $this->setThemeByRole('main');

            if (empty($this->theme)) {
                return [
                    'status'  => 'error',
                    'message' => 'Erro na permissão de tema',
                ];
            }

            $this->client->getAssetManager()->update($this->theme->getId(), [
                "key"   => 'templates/404.liquid',
                "value" => $this->getTemplateHtml('templates/404.liquid'),
            ]);

            return [
                'status' => 'success',
            ];
        } catch (Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Erro na permissão de tema',
            ];
        }
    }

    /**
     * @param bool $skipToCart
     */
    public function setSkipToCart(bool $skipToCart): void
    {
        $this->skipToCart = $skipToCart;
    }

    public function updateOrder(Sale $sale)
    {
        try {
            $this->method = __METHOD__;
            $this->saleId = $sale->id;
            if (!empty($sale) && !empty($sale->shopify_order)) {
                $client   = $sale->customer;

                $shippingAddress = [
                    "phone" => $client->telephone,

                ];

                $orderData = [
                    "email"            => $client->email,
                    "phone"            => $client->telephone,
                    "shipping_address" => $shippingAddress,

                ];

                $this->sendData = $orderData;
                $order          = $this->client->put('orders/' . $sale->shopify_order, [
                    'order'    => $orderData,
                ]);

                $this->receivedData = $order;
            } else {
                Log::emergency('Erro ao atualizar uma ordem no shopify com a venda ' . $sale->id);
            }
        } catch (Exception $e) {
            $this->exceptions[] = $e->getMessage();
            Log::emergency('Erro ao atualizar uma ordem no shopify com a venda ' . $sale->id);
        }
    }
}


