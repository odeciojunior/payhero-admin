<?php

namespace Modules\Core\Services;

use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;
use PHPHtmlParser\Selector\Parser;
use PHPHtmlParser\Selector\Selector;
use Slince\Shopify\Client;
use Slince\Shopify\PublicAppCredential;
use Exception;

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
    private $theme;

    /**
     * ShopifyService constructor.
     * @param $urlStore
     * @param $token
     */
    public function __construct(string $urlStore, string $token)
    {
        if ($this->cacheDir) {
            $cache = './tmp';
        } else {
            $cache = $this->cacheDir;
        }

        $this->credential = new PublicAppCredential($token);
        $this->client     = new Client($this->credential, $urlStore, [
            'metaCacheDir' => $cache // Metadata cache dir, required
        ]);
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
     * @return $this
     */
    public function setThemeByRole($role)
    {
        $this->theme = $this->getThemeByRole($role);

        return $this;
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
    public function updateTemplateHtml(string $templateKeyName, string $value, $ajax = false)
    {
        if (!empty($this->theme)) {
            if ($ajax) {
                $asset = $this->client->getAssetManager()->update($this->theme->getId(), [
                    "key"   => $templateKeyName,
                    "value" => $this->updateCartTemplateAjax($value),
                ]);
            } else {
                $asset = $this->client->getAssetManager()->update($this->theme->getId(), [
                    "key"   => $templateKeyName,
                    "value" => $this->updateCartTemplate($value),
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
     * @param $htmlCart
     * @return mixed|string
     * @throws \PHPHtmlParser\Exceptions\CircularException
     */
    public function updateCartTemplateAjax($htmlCart)
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

    /**
     * @param $htmlCart
     * @return mixed|string
     * @throws \PHPHtmlParser\Exceptions\CircularException
     */
    public function updateCartTemplate($htmlCart)
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
}
