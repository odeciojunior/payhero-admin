<?php

namespace App\Http\Controllers\Dev;

use App\Entities\User;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use Modules\Core\Services\DigitalOceanFileService;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\Tag;
use PHPHtmlParser\Dom\TextNode;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Selector\Parser;
use PHPHtmlParser\Selector\Selector;

class TesteController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //$data['product_digital_path'] = $this->uploadFiles($folder, $this->extensionPathsAttachment, 'Attachment', $pathCheckOld, 'gcsPrivate');


        /*$update  = [
            'url_logo' => env('DO_SPACES_ENDPOINT') . '/' . env('DO_SPACES_BUCKET') . '/' . $fileurl,
        ];*/

/*

        $teste->uploadFile('upload', '/var/www/fox/admin/public/favicon.ico');
        dd($teste->disk());
*/
/*
        $x = parse_url('http://a.com/uploads/user/wqP5LNZ8VgaRye0/public/profile/7OGabBYpgwN7cyzFQVoj1jkSaw7D7AeCxc1Ylbvj.jpeg');
        dd($x);
*/

        $teste = app(DigitalOceanFileService::class);
        $url = $teste->deleteFile('uploads/user/wqP5LNZ8VgaRye0/public/profile/mdNX4HV84XzFwp01sQSxoWXCv5JJl8JZIVn6vT9C.jpeg');
        $files = Storage::disk('openSpaces')->files('uploads/user/' . Hashids::encode(auth()->user()->id) . '/public/profile/');
        dd($files);





        //composer require league/flysystem-aws-s3-v3






        $html = "{% comment %}

  This snippet provides the default handlebars.js templates for
  the ajaxify cart plugin. Use the raw liquid tags to keep the
  handlebar.js template tags as available hooks.

{% endcomment %}
  <script id='cartTemplate' type='text/template'>
  {% raw %}
    <form action='/cart' method='post' class='cart-form' novalidate>
      <div class='ajaxifyCart--products'>
        {{#items}}
        <div class='ajaxifyCart--product'>
          <div class='ajaxifyCart--row' data-line='{{line}}'>
            <div class='grid'>
              <div class='grid-item large--two-thirds'>
                <div class='grid'>
                  <div class='grid-item one-quarter'>
                    <a href='{{url}}' class='ajaxCart--product-image'><img src='{{img}}' alt=''></a>
                  </div>
                  <div class='grid-item three-quarters'>
                    <a href='{{url}}' class='h4'>{{name}}</a>
                    <p>{{variation}}</p>
                  </div>
                </div>
              </div>
              <div class='grid-item large--one-third'>
                <div class='grid'>
                  <div class='grid-item one-third'>
                    <div class='ajaxifyCart--qty'>
                      <input type='text' name='updates[]' class='ajaxifyCart--num' value='{{itemQty}}' min='0' data-line='{{line}}' aria-label='quantity' pattern='[0-9]*'>
                      <span class='ajaxifyCart--qty-adjuster ajaxifyCart--add' data-line='{{line}}' data-qty='{{itemAdd}}'>+</span>
                      <span class='ajaxifyCart--qty-adjuster ajaxifyCart--minus' data-line='{{line}}' data-qty='{{itemMinus}}'>-</span>
                    </div>
                  </div>
                  <div class='grid-item one-third text-center'>
                    <p>{{price}}</p>
                  </div>
                  <div class='grid-item one-third text-right'>
                    <p>
                      <small><a href='/cart/change?line={{line}}&amp;quantity=0' class='ajaxifyCart--remove' data-line='{{line}}'>Remove</a></small>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        {{/items}}
      </div>
      <div class='ajaxifyCart--row text-right medium-down--text-center'>
        <span class='h3'>Subtotal {{totalPrice}}</span>
        <input type='submit' class='{{btnClass}}' name='checkout' value='Checkout'>
      </div>
    </form>
  {% endraw %}
  </script>
  <script id='drawerTemplate' type='text/template'>
  {% raw %}
    <div id='ajaxifyDrawer' class='ajaxify-drawer'>
      <div id='ajaxifyCart' class='ajaxifyCart--content {{wrapperClass}}'></div>
    </div>
    <div class='ajaxifyDrawer-caret'><span></span></div>
  {% endraw %}
  </script>
  <script id='modalTemplate' type='text/template'>
  {% raw %}
    <div id='ajaxifyModal' class='ajaxify-modal'>
      <div id='ajaxifyCart' class='ajaxifyCart--content'></div>
    </div>
  {% endraw %}
  </script>
  <script id='ajaxifyQty' type='text/template'>
  {% raw %}
    <div class='ajaxifyCart--qty'>
      <input type='text' class='ajaxifyCart--num' value='{{itemQty}}' data-id='{{key}}' min='0' data-line='{{line}}' aria-label='quantity' pattern='[0-9]*'>
      <span class='ajaxifyCart--qty-adjuster ajaxifyCart--add' data-id='{{key}}' data-line='{{line}}' data-qty='{{itemAdd}}'>+</span>
      <span class='ajaxifyCart--qty-adjuster ajaxifyCart--minus' data-id='{{key}}' data-line='{{line}}' data-qty='{{itemMinus}}'>-</span>
    </div>
  {% endraw %}
  </script>
  <script id='jsQty' type='text/template'>
  {% raw %}
    <div class='js-qty'>
      <input type='text' class='js--num' value='{{itemQty}}' min='1' data-id='{{key}}' aria-label='quantity' pattern='[0-9]*' name='{{inputName}}' id='{{inputId}}'>
      <span class='js--qty-adjuster js--add' data-id='{{key}}' data-qty='{{itemAdd}}'>+</span>
      <span class='js--qty-adjuster js--minus' data-id='{{key}}' data-qty='{{itemMinus}}'>-</span>
    </div>
  {% endraw %}
  </script>
";

        $dom = new Dom;

        $dom->setOptions([
                             'removeScripts' => false, // Set a global option to enable strict html parsing.
                         ]);

        $dom->load($html);

        $forms = $dom->find('script[id=cartTemplate]');
        $x=$forms->innerHtml();

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

            /*
             * $( "[data-integration-price-saved=1]" ).each(function( key, value ) {
x = value.innerText;
console.log(x);
});
             */

            dd($html);
        } else {
            //thown parse error
        }
        //        dd($dom->root->outerHtml());
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
