<?php

namespace Modules\Core\Services;

use Exception;
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
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;
use PHPHtmlParser\Selector\Selector;
use Modules\Core\Entities\ProductPlan;
use Slince\Shopify\Manager\Asset\Asset;
use Slince\Shopify\Manager\Theme\Theme;
use Slince\Shopify\PublicAppCredential;
use PHPHtmlParser\Exceptions\CurlException;
use Slince\Shopify\Manager\Webhook\Webhook;
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
     * ShopifyService constructor.
     * @param string $urlStore
     * @param string $token
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
        /** @var \Slince\Shopify\Theme\Theme[] $themes */
        $themes = $this->getAllThemes();
        /** @var \Slince\Shopify\Theme\Theme $theme */
        foreach ($themes as $theme) {
            if ($theme->getRole() == $role)
                return $theme;
        }

        return null; //throwl
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

        if ($cartForm) {

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

        if ($cartForm) {
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

        $planModel = new Plan();

        $productModel = new Product();

        $productPlanModel = new ProductPlan();

        $storeProduct = $this->getShopProduct($shopifyProductId);

        if (empty($storeProduct)) {
            return false;
        }

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
            } catch (Exception $e) {
                //
            }
            $product = $productModel->with('productsPlans')
                                    ->where('shopify_id', $storeProduct->getId())
                                    ->where('shopify_variant_id', $variant->getId())
                                    ->first();
            if ($product) {

                $product->update(
                    [
                        'name'               => FoxUtils::removeSpecialChars(FoxUtils::removeAccents(substr($storeProduct->getTitle(), 0, 100))),
                        'description'        => FoxUtils::removeSpecialChars(FoxUtils::removeAccents(substr($description, 0, 100))),
                        'weight'             => $variant->getWeight(),
                        'cost'               => $this->getShopInventoryItem($variant->getInventoryItemId())
                                                     ->getCost(),
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
                            'name'        => FoxUtils::removeSpecialChars(FoxUtils::removeAccents(substr($storeProduct->getTitle(), 0, 100))),
                            'description' => FoxUtils::removeSpecialChars(FoxUtils::removeAccents(substr($description, 0, 100))),
                            'price'       => $variant->getPrice(),
                            'status'      => '1',
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
                            'name'               => FoxUtils::removeSpecialChars(FoxUtils::removeAccents(substr($storeProduct->getTitle(), 0, 100))),
                            'description'        => FoxUtils::removeSpecialChars(FoxUtils::removeAccents(substr($description, 0, 100))),
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
                        'name'               => FoxUtils::removeSpecialChars(FoxUtils::removeAccents(substr($storeProduct->getTitle(), 0, 100))),
                        'description'        => FoxUtils::removeSpecialChars(FoxUtils::removeAccents(substr($description, 0, 100))),
                        'guarantee'          => '0',
                        'format'             => 1,
                        'category_id'        => '11',
                        'cost'               => $this->getShopInventoryItem($variant->getInventoryItemId())
                                                     ->getCost(),
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
                        'name'               => FoxUtils::removeSpecialChars(FoxUtils::removeAccents(substr($storeProduct->getTitle(), 0, 100))),
                        'description'        => FoxUtils::removeSpecialChars(FoxUtils::removeAccents(substr($description, 0, 100))),
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
}


