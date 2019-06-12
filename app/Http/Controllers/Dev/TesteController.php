<?php

namespace App\Http\Controllers\Dev;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $html = "<div class='page-width' data-section-id='{{ section.id }}' data-section-type='cart-template'>

  {% if cart.item_count > 0 %}
  {% if section.settings.cart_enable %}
  {{ 'jquery.countdownTimer_2hours.js' | asset_url | script_tag }}  

  {% endif %}
  <form action='/cart' method='post' novalidate class='cart2 xxxx yyyy'>
  </form>

    <form action='/cart' method='post' novalidate class='cart'>
      <div class='section-header_1 grid'>
        <div class='grid__item medium-up--one-half small--text-center medium-up--text-left'>
          <h3 class='heading'>{{ 'cart.general.title' | t }}</h3>
        </div>
        <div class='grid__item medium-up--one-half small--text-center medium-up--text-right'>
          <button name='checkout' type='submit' class='marg_btm checkout_btn btn_sp'>
            <img src='https://cdn.shopify.com/s/files/1/3096/8844/files/icon_1.png'> 
            <span>{{ 'cart.general.checkout' | t }}</span>
          </button>
        </div>
      </div>
      <table>
        <thead class='cart__row cart__header'>
          <th colspan='2' class='text-center set_align'>{{ 'cart.label.product' | t }}</th>
          <th>{{ 'cart.label.quantity' | t }}</th>
          <th class='text-center'>{{ 'cart.label.price' | t }}</th>          
          <th class='text-right'>{{ 'cart.label.total' | t }}</th>
        </thead>
        <tbody>
          {% for item in cart.items %}
          
          {% if item.variant.compare_at_price > item.variant.price %}
          {% assign saving = item.variant.compare_at_price | minus: item.variant.price | times: item.quantity %}
          {% assign was = item.variant.compare_at_price |  times: item.quantity %}
          {% assign total_saving = saving | plus: total_saving %}
          {% assign total_saving_was = was | plus: total_saving_was %}        
          {% endif %}
          
            <tr class='cart__row border-bottom line{{ forloop.index }} cart-flex{% if forloop.first %} border-top{% endif %}'>
            <input type='hidden' data-fox='1' name='product_id_{{ forloop.index }}' value='{{ item.id }}'>
              <td class='cart__image-wrapper cart-flex-item'>
                <a href='{{ item.url | within: collections.all }}'>
                  <img class='cart__image' src='{{ item | img_url: '95x95', scale: 2 }}' alt='{{ item.title | escape }}'>
                </a>
              </td>
              <td class='cart__meta small--text-left cart-flex-item'>
                <div class='list-view-item__title'>
                  <a href='{{ item.url }}'>
                    {{ item.product.title }}
<span class='booster-cart-item-success-notes' data-key='{{item.key}}'></span><span class='booster-cart-item-upsell-notes' data-key='{{item.key}}'></span>
                    {% if item.quantity > 1 %}
                      <span class='medium-up--hide'><span class='visually-hidden'>{{ 'cart.label.quantity' | t }}</span>(x{{ item.quantity }})</span>
                    {% endif %}
                  </a>
                </div>
                {% unless item.variant.title contains 'Default' %}
                  <div class='cart__meta-text'>
                    {% for option in item.product.options %}
                      {{ option }}: {{ item.variant.options[forloop.index0] }}<br/>
                    {% endfor %}
                  </div>
                {% endunless %}

                {% comment %}
                  Optional, loop through custom product line items if available

                  Line item properties come in as having two parts. The first part will be passed with the default form,
                  but p.last is the actual custom property and may be blank. If it is, don't show it.

                  For more info on line item properties, visit:
                    - http://docs.shopify.com/support/your-store/products/how-do-I-collect-additional-information-on-the-product-page-Like-for-a-monogram-engraving-or-customization
                {% endcomment %}
                {%- assign property_size = item.properties | size -%}
                {% if property_size > 0 %}
                  <div class='cart__meta-text'>
                    {% for p in item.properties %}
                      {% unless p.last == blank %}
                        {{ p.first }}:

                        {% comment %}
                          Check if there was an uploaded file associated
                        {% endcomment %}
                        {% if p.last contains '/uploads/' %}
                          <a href='{{ p.last }}'>{{ p.last | split: '/' | last }}</a>
                        {% else %}
                          {{ p.last }}
                        {% endif %}
                      {% endunless %}
                    {% endfor %}
                  </div>
                {% endif %}

                <p class='small--hide'>
                  <a href='/cart/change?line={{ forloop.index }}&amp;quantity=0' class='btn btn--small btn--secondary cart__remove'>{{ 'cart.general.remove' | t }}</a>
                </p>
              </td>
              <td class='cart__price-wrapper cart-flex-item text-center medium-up--hide'>
                <span class='hulkapps-cart-item-price' data-key='{{item.key}}'>{{ item.price | money }}</span>

                {% for discount in item.discounts %}
                  <div class='cart-item__discount medium-up--hide'>{{ discount.title }}</div>
                {% endfor %}

                <div class='cart__edit medium-up--hide'>
                  <button type='button' class='btn btn--secondary btn--small js-edit-toggle cart__edit--active' data-target='line{{ forloop.index }}'>
                    <span class='cart__edit-text--edit'>{{ 'cart.general.edit' | t }}</span>
                    <span class='cart__edit-text--cancel'>{{ 'cart.general.cancel' | t }}</span>
                  </button>
                </div>
              </td>
              <td class='cart__update-wrapper cart-flex-item'>
                <a href='/cart/change?line={{ forloop.index }}&amp;quantity=0' class='btn btn--small btn--secondary cart__remove medium-up--hide'>{{ 'cart.general.remove' | t }}</a>
                <div class='cart__qty'>
<!--                   <label for='updates_{{ item.key }}' class='cart__qty-label'>{{ 'cart.label.quantity' | t }}</label> -->
                  <input class='cart__qty-input' type='number' name='updates[]' id='updates_{{ item.key }}' value='{{ item.quantity }}' min='0' pattern='[0-9]*'>
                </div>
                <input type='submit' name='update' class='btn btn--small btn--secondary cart__update medium-up--hide' value='{{ 'cart.general.update' | t }}'>
              </td>
              <td class='cart__price-wrapper cart-flex-item text-center small--hide'>
                <span class='hulkapps-cart-item-price' data-key='{{item.key}}'>{{ item.price | money }}</span>

                {% for discount in item.discounts %}
                  <div class='cart-item__discount medium-up--hide'>{{ discount.title }}</div>
                {% endfor %}

                <div class='cart__edit medium-up--hide'>
                  <button type='button' class='btn btn--secondary btn--small js-edit-toggle cart__edit--active' data-target='line{{ forloop.index }}'>
                    <span class='cart__edit-text--edit'>{{ 'cart.general.edit' | t }}</span>
                    <span class='cart__edit-text--cancel'>{{ 'cart.general.cancel' | t }}</span>
                  </button>
                </div>
              </td>
              <td class='text-right small--hide'>
                {% if item.original_line_price != item.line_price %}
                  <div class='cart-item__original-price'><s>{{ item.original_line_price | money }}</s></div>
                {% endif %}

                <div>
                  <span class='' data-key='{{item.key}}'><span class='booster-cart-item-line-price' data-key='{{item.key}}'>{{ item.line_price | money }}</span></span>
                </div>

                {% for discount in item.discounts %}
                  <div class='cart-item__discount'>{{ discount.title }}</div>
                {% endfor %}
              </td>
              <script>
              </script>
            </tr>
          {% endfor %}
        </tbody>
      </table>

      <footer class='cart__footer'>
        <div class='grid table_medium_up'>
          {% if section.settings.cart_notes_enable %}
            <div class='grid__item medium-up--one-half cart-note small--hide'>
              <label for='CartSpecialInstructions' class='cart-note__label small--text-center'>{{ 'cart.general.note' | t }}</label>
              <textarea name='note' id='CartSpecialInstructions' class='cart-note__input'>{{ cart.note }}</textarea>
              <div class='medium-up--hide'>
          <a href='collections/all' class='btn cart__update cart__continue--large' >{{ 'cart.general.continue_shopping' | t }}</a>  
              </div>
              </div>
          {% endif %}
          <div class='grid__item totle_cart text-right{% if section.settings.cart_notes_enable %} medium-up--one-half{% endif %}'>
            <div>
              {% assign x = total_saving | plus: 0  %}
              {% assign y = x | plus: cart.total_price | plus: 0  %}


              {% assign sale = total_saving | times: 100.0  %}
              {% assign sale = sale | divided_by:  y | round | append: '%' %}


              {% if section.settings.saved_price  %}
              {% if total_saving %}
              <div class='td_price'>
                <span class='cart__subtotal-title' >
                  {{ 'cart.general.yousave' | t }}
                </span>
                <span class='saved_prc cart__subtotal'><span class='cart-sale_price'>{{ sale }} OFF</span></span>
              </div>
              {% endif %}
              {% endif %}	
              
              <span class='cart__subtotal-title'>{{ 'cart.general.subtotal' | t }}</span>
              <span class='cart__subtotal'><span class='crt_total'><span class=''><span class='wh-original-cart-total'><span class='wh-original-price'>{{ cart.total_price | money }}</span></span><span class='wh-cart-total'></span><div class='additional-notes'><span class='wh-minimums-note'></span><span class='wh-extra-note'></span></div></span></span></span>
            </div>
            {% if cart.total_discounts > 0 %}
              <div class='cart__savings'>
                {{ 'cart.general.savings' | t }}
                <span class='cart__savings-amount'>{{ cart.total_discounts | money }}</span>
              </div>
            {% endif %}
            <div class='cart__shipping'>{{ 'cart.general.shipping_at_checkout' | t }}</div>
              
            {% if section.settings.cart_enable %}
        
        
          

          <script>
            
            
            var time_minute={{ section.settings.cart_timer_minute }}/60 ;
         //   alert(time_minute);
            var hours = Math.floor( time_minute / 60); 
            var minutes = Math.floor(time_minute % 60);     

            
            $(function(){
              $('#hm_timer120').countdowntimer({
                hours : hours,
                minutes :minutes,
                seconds :00
              });
            });
          </script>


        <p class='timer_box'><span class='cart_text'>{{ 'cart.general.cart_expire_text' | t }} </span>
          <span id='hm_timer120' class='cart_time'></span></p>
        
        {% endif %}
          </div>
          
        </div>
        <div class='grid table_medium_up'>
          <div class='grid__item medium-up--one-half continue_shopping'>
            <a href='collections/all' class='btn btn--secondary cart__update cart__continue--large' >{{ 'cart.general.continue_shopping' | t }}</a>
          </div>
          <div class='grid__item medium-up--one-half text-right'>
            <input type='submit' name='update' class='btn btn--secondary cart__update cart__update--large small--hide' value='{{ 'cart.general.update' | t }}'>
            <button name='checkout' type='submit' class='marg_btm checkout_btn btn_sp btn--small-wide'>
            <img src='https://cdn.shopify.com/s/files/1/3096/8844/files/icon_1.png'> 
            <span>{{ 'cart.general.checkout' | t }}</span>
          </button>
            
            {% if additional_checkout_buttons and  section.settings.additional_button %}
            <div class='additional-checkout-buttons'>{{ content_for_additional_checkout_buttons }}</div>
            {% endif %}
          </div>
        </div>
        <div class='grid'>
          <p class='grid__item currency_info small--text-center'>
            {{ 'cart.general.currency_info_text1' | t: shop_name: shop.name, shop_currency: shop.currency }} <span class='selected-currency'></span>, {{ 'cart.general.currency_info_text2' | t: shop_currency: shop.currency }}
          </p>

        </div>
        {% if section.settings.shopping_payment %}
        <div class='cart__row cart_boxS grid grid--no-gutters'>
            <div class='grid__item medium-up--one-half small--text-center checkout-logos'>
              <div class='we-accept'>
                <p> {{ 'cart.general.secure_shopping_image_title' | t }} </p>

                {% unless section.settings.cart_left_image == blank %}
                 <img src='{{ section.settings.cart_left_image | img_url: '500x100' }}'>
                {% else %}
                <img src='{{ 'pay_right.png' | asset_img_url: '500x100' }}'>               
                {% endunless %}     

              </div>
            </div> 

            <div class='grid__item medium-up--one-half small--text-center medium-up--text-right secure-shopping'>
              <p> {{ 'cart.general.payment_image_title' | t }}  </p>
              {% if section.settings.cart_right_image == blank %}
              <img src='{{ 'pay_icn.jpg' | asset_img_url: '500x100' }}'>
              {% else %}
              <img src='{{ section.settings.cart_right_image | img_url: '500x100' }}'>
              {% endif %}     

            </div>

          </div>
        {% endif %}
      </footer>
      <script>
      </script>
    </form>
  {% else %}
    <div class='empty-page-content text-center'>
      <h1>{{ 'cart.general.title' | t }}</h1>
      <p class='cart--empty-message'>{{ 'cart.general.empty' | t }}</p>
      <div class='cookie-message'>
        <p>{{ 'cart.general.cookies_required' | t }}</p>
      </div>
      <a href='/' class='btn btn--has-icon-after cart__continue-btn'>{{ 'general.404.link' | t }}{% include 'icon-arrow-right' %}</a>
    </div>
  {% endif %}
</div>
<a href='/cart/clear' class='clear-cart my-super-fancy-button-style'>Clear All Items From Cart</a>
 {% if cart.item_count > 0 %}
{% if section.settings.cart_enable %}
<script>

  $('.clear-cart').on('click',function(e){    
    e.preventDefault();
    $.ajax({
      type: 'POST',
      url: '/cart/clear.js',
      success: function(){
        window.location.href = '/cart/';
      },
      dataType: 'json'
    });
  });
  var time_minute_1 ={{ section.settings.cart_timer_minute }}/60 ;
  //   alert(time_minute);
  var hours_1 = Math.floor( time_minute_1 / 60) * 60;
  var minutes_1 = Math.floor(time_minute_1 % 60); 
  var totalminute = minutes_1 + hours_1;
  var milisecond = (totalminute * 60) * 1000;
  $(window).load(function(){
    setTimeout(function() {
      $('.clear-cart').trigger('click');     
    }, milisecond);

  });			
  </script>
<style>
  .timer_box span.cart_time {
    color: {{ section.settings.cart_timer_color }} !important;
  }
  </style>
{% endif %}
{% endif %}
{% schema %}
  {
    'name': 'Cart page',
    'settings': [
      {
        'type': 'checkbox',
        'id': 'cart_notes_enable',
        'label': 'Enable cart notes',
        'default': false
      },
 	  {
        'type': 'checkbox',
        'id': 'additional_button',
        'label': 'Enable additional checkout button',
        'default': true
      },
 	  {
        'type': 'checkbox',
        'id': 'saved_price',
        'label': 'Enable Saved Percentage',
        'default': true
      },
      {
        'type': 'header',
        'content': 'Cart Page Timer'
      },
      {
        'type': 'checkbox',
        'id': 'cart_enable',
        'label': 'Timer Enable'
      },
      {
        'type': 'number',
        'id': 'cart_timer_minute',
        'label': 'Cart Timer Minute',
        'info': 'Example: 1500 second left = 25 minutes left'
      },
      {
        'type': 'color',
        'id': 'cart_timer_color',
        'label': 'Cart Timer Color'
      },
      {
        'type': 'header',
        'content': 'cart page images setting'
      },
 	  {
        'type': 'checkbox',
        'id': 'shopping_payment',
        'label': 'Enable secure shopping and payment card images',
        'default': true
      },
      {
        'type': 'image_picker',
        'id': 'cart_left_image',
        'label': 'Left image',
        'info': 'Maximum logo dimensions are 500px wide by 100px high. The uploaded file will be resized to fit within those constraints.'
      },
      {
        'type': 'image_picker',
        'id': 'cart_right_image',
        'label': 'Right image',
        'info': 'Maximum logo dimensions are 500px wide by 100px high. The uploaded file will be resized to fit within those constraints.'
      }
    ]
  }
{% 
endschema
 %}
";

        $dom = new Dom;
        $dom->load($html);

        $forms = $dom->find('form');
        foreach ($forms as $form) {
            $data = explode(' ', $form->getAttribute('class'));
            if (in_array('cart', $data)) {
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
                    $newHtml = substr_replace($html, $foxData, $item[1] + strlen($item[0]), 0);
                }
            }

            /*
             * $( "[data-integration-price-saved=1]" ).each(function( key, value ) {
x = value.innerText;
console.log(x);
});
             */

            dd($newHtml);
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
