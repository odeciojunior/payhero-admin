<?php

namespace Modules\Shopify\Http\Controllers;

use App\Plano;
use App\Venda;
use Exception;
use App\Empresa;
use App\Entrega;
use App\Produto;
use App\Projeto;
use App\Comprador;
use App\PlanoVenda;
use App\UserProjeto;
use App\ProdutoPlano;
use App\IntegracaoShopify;
use Slince\Shopify\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Adapter\Guzzle;
use Illuminate\Routing\Controller;
use Cloudflare\API\Endpoints\Zones;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Slince\Shopify\PublicAppCredential;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class ShopifyController extends Controller {


    public function index() {

        $empresas = Empresa::where('user',\Auth::user()->id)->get()->toArray();

        $integracoesShopify = IntegracaoShopify::where('user',\Auth::user()->id)->get()->toArray();

        $projetos = [];

        foreach($integracoesShopify as $integracaoShopify){

            $projeto = Projeto::find($integracaoShopify['projeto']);

            if($projeto){
                $projetos[] = $projeto;
            }
        }

        return view('shopify::index',[
            'empresas' => $empresas,
            'projetos' => $projetos,
        ]);
    }

    public function adicionarIntegracao(Request $request){

        $dados = $request->all();

        $integracaoShopify = IntegracaoShopify::where('token',$dados['token'])->first();

        if($integracaoShopify != null){
            return response()->json('Projeto já integrado');
        }

        try{
            $credential = new PublicAppCredential($dados['token']);

            $client = new Client($credential, $dados['url_loja'], [
                'metaCacheDir' => './tmp' // Metadata cache dir, required
            ]);
        }
        catch(\Exception $e){
            return response()->json('Dados do shopify inválidos, revise os dados informados');
        }

        try{
            $projeto = Projeto::create([
                'nome'                  => $client->getShopManager()->get()->getName(),
                'status'                => '1',
                'visibilidade'          => 'privado',
                'porcentagem_afiliados' => '0',
                'descricao'             =>  $client->getShopManager()->get()->getName(),
                'descricao_fatura'      => $client->getShopManager()->get()->getName(),
                'url_pagina'            => 'https://'.$client->getShopManager()->get()->getDomain(),
                'afiliacao_automatica'  => false,
                'shopify_id'            => $client->getShopManager()->get()->getId(),
            ]);
        }
        catch(\Exception $e){
            return response()->json('Dados do shopify inválidos, revise os dados informados');
        }

        $themes = $client->getThemeManager()->findAll([]);

        foreach($themes as $theme){
            if($theme->getRole() == 'main')
                break;
        }

        $asset = $client->getAssetManager()->update($theme->getId(),[
            "key"   => "sections/cart-template.liquid",
            "value" => $this->getCartTemplate()
        ]);

        $imagem = $request->file('foto_projeto');

        if ($imagem != null) {
            $nomeFoto = 'projeto_' . $projeto->id . '_.' . $imagem->getClientOriginalExtension();

            $imagem->move(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO, $nomeFoto);

            try{
              $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO . $nomeFoto);

              $img->crop($dados['foto_w'], $dados['foto_h'], $dados['foto_x1'], $dados['foto_y1']);

              $img->resize(200, 200);

              Storage::delete('public/upload/projeto/'.$nomeFoto);

              $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO . $nomeFoto);

              $projeto->update([
                  'foto' => $nomeFoto
              ]);
            }
            catch(\Exception $e){
                // não cadastra imagem
            }
        }

        UserProjeto::create([
            'user'              => \Auth::user()->id,
            'projeto'           => $projeto->id,
            'empresa'           => $dados['empresa'],
            'tipo'              => 'produtor',
            'responsavel_frete' => true,
            'permissao_acesso'  => true,
            'permissao_editar'  => true,
            'status'            => 'ativo'
        ]);

        $products = $client->getProductManager()->findAll([]);

        foreach($products as $product){

            foreach($product->getVariants() as $variant){

                $produto = Produto::create([
                    'user'          => \Auth::user()->id,
                    'nome'          => substr($product->getTitle(),0,100),
                    'descricao'     => '',
                    'garantia'      => '0',
                    'disponivel'    => true,
                    'quantidade'    => '0',
                    'formato'       => 1,
                    'categoria'     => '11',
                    'custo_produto' => '',
                    'shopify'       => true,
                ]);

                $novoCodigoIdentificador = false;

                while($novoCodigoIdentificador == false){

                    $codigoIdentificador = $this->randString(3).rand(100,999);

                    $plano = Plano::where('cod_identificador', $codigoIdentificador)->first();

                    if($plano == null){
                        $novoCodigoIdentificador = true;
                    }
                }

                $descricao = '';

                try{
                    $descricao = $variant->getOption1();
                    if($descricao == 'Default Title'){
                        $descricao = '';
                    }
                    if($variant->getOption2() != ''){
                        $descricao .= ' - '. $$variant->getOption2();
                    }
                    if($variant->getOption3() != ''){
                        $descricao .= ' - '. $$variant->getOption3();
                    }
                }
                catch(\Exception $e){
                    //
                }

                $plano = Plano::create([
                    'shopify_id'            => $product->getId(),
                    'shopify_variant_id'    => $variant->getId(),
                    'empresa'               => $dados['empresa'],
                    'projeto'               => $projeto->id,
                    'nome'                  => substr($product->getTitle(),0,100),
                    'descricao'             => $descricao,
                    'cod_identificador'     => $codigoIdentificador,
                    'preco'                 => $variant->getPrice(),
                    'frete_fixo'            => '1',
                    'valor_frete'           => '0.00',
                    'pagamento_cartao'      => true,
                    'pagamento_boleto'      => true,
                    'status'                => '1',
                    'transportadora'        => '2',
                    'qtd_parcelas'          => '12',
                    'parcelas_sem_juros'    => '1'
                ]);

                if(count($product->getVariants()) > 1){
                    foreach($product->getImages() as $image){

                        foreach($image->getVariantIds() as $variantId){
                            if($variantId == $variant->getId()){

                                if($image->getSrc() != ''){
                                    $produto->update([
                                        'foto' => $image->getSrc()
                                    ]);

                                    $plano->update([
                                        'foto' => $image->getSrc()
                                    ]);
                                }
                                else{
                                    $plano->update([
                                        'foto' => $product->getImage()->getSrc()
                                    ]);
                
                                    $produto->update([
                                        'foto' => $product->getImage()->getSrc()
                                    ]);
                                }
                            }
                        }
                    }
                }
                else{

                    $plano->update([
                        'foto' => $product->getImage()->getSrc()
                    ]);

                    $produto->update([
                        'foto' => $product->getImage()->getSrc()
                    ]);
                }

                ProdutoPlano::create([
                    'produto'            => $produto->id,
                    'plano'              => $plano->id,
                    'quantidade_produto' => '1'
                ]);
            }

        }

        $client->getWebhookManager()->create([
            "topic"   => "products/create",
            "address" => "https://cloudfox.app/aplicativos/shopify/webhook/".$projeto['id'],
            "format"  => "json"
        ]);

        $client->getWebhookManager()->create([
            "topic"   => "products/update",
            "address" => "https://cloudfox.app/aplicativos/shopify/webhook/".$projeto['id'],
            "format"  => "json"
        ]);

        IntegracaoShopify::create([
            'token' => $dados['token'],
            'url_loja' => $dados['url_loja'],
            'user' => \Auth::user()->id,
            'projeto' => $projeto->id
        ]);

        return response()->json('Sucesso');
    }

    public function getCartTemplate(){

        return "<div class='page-width' data-section-id='{{ section.id }}' data-section-type='cart-template'>

        {% if cart.item_count > 0 %}
        {% if section.settings.cart_enable %}
        {{ 'jquery.countdownTimer_2hours.js' | asset_url | script_tag }}  
      
        {% endif %}
      
          <form action='https://checkout.{{ shop.domain }}/' method='post' novalidate class='cart' id='formulario_carrinho'>
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
                  <input type='hidden' name='id_produto_{{ forloop.index }}' value='{{ item.id }}'>
                  <input type='hidden' name='id_variant_{{ forloop.index }}' value='{{ item.variant_id }}'>
                    <input type='hidden' name='preco_produto_{{ forloop.index }}' value='{{ item.price }}'>
                    <input type='hidden' name='imagem_produto_{{ forloop.index }}' value='{{ item.image }}'>
      
                  <tr class='cart__row border-bottom line{{ forloop.index }} cart-flex{% if forloop.first %} border-top{% endif %}'>
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
                        <input class='cart__qty-input' type='text' id='updates_{{ item.key }}' value='{{ item.quantity }}' min='0' pattern='[0-9]*' disabled>
                        <input type='hidden' name='qtd_produto_{{ forloop.index }}' value='{{ item.quantity }}'>
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
      
                  $(document).ready(function (){
      
      
                    $('.wh-original-cart-total').on('DOMSubtreeModified',function(){
      
                      var input_adicionado = false;
      
                      if(!input_adicionado && typeof $('.spurit-tired-pricing-subtotal-yousave').html() !== 'undefined'){
                        var desconto = $('.spurit-tired-pricing-subtotal-yousave').html().replace(/[^0-9]/g,'');
      
                        if(desconto != '' && desconto > 100){
                          $('#formulario_carrinho').append('<input type='hidden' name='desconto_quantidade' value=''+desconto+''>');
                          input_adicionado = true;
                        }
      
                      }
      
                    });
      
                  });
      
                  var time_minute={{ section.settings.cart_timer_minute }}/60 ;
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
        \"name\": \"Cart page\",
        \"settings\": [
          {
            \"type\": \"checkbox\",
            \"id\": \"cart_notes_enable\",
            \"label\": \"Enable cart notes\",
            \"default\": false
          },
          {
            \"type\": \"checkbox\",
            \"id\": \"additional_button\",
            \"label\": \"Enable additional checkout button\",
            \"default\": true
          },
          {
            \"type\": \"checkbox\",
            \"id\": \"saved_price\",
            \"label\": \"Enable Saved Percentage\",
            \"default\": true
          },
          {
            \"type\": \"header\",
            \"content\": \"Cart Page Timer\"
          },
          {
            \"type\": \"checkbox\",
            \"id\": \"cart_enable\",
            \"label\": \"Timer Enable\"
          },
          {
            \"type\": \"number\",
            \"id\": \"cart_timer_minute\",
            \"label\": \"Cart Timer Minute\",
            \"info\": \"Example: 1500 second left = 25 minutes left\"
          },
          {
            \"type\": \"color\",
            \"id\": \"cart_timer_color\",
            \"label\": \"Cart Timer Color\"
          },
          {
            \"type\": \"header\",
            \"content\": \"cart page images setting\"
          },
          {
            \"type\": \"checkbox\",
            \"id\": \"shopping_payment\",
            \"label\": \"Enable secure shopping and payment card images\",
            \"default\": true
          },
          {
            \"type\": \"image_picker\",
            \"id\": \"cart_left_image\",
            \"label\": \"Left image\",
            \"info\": \"Maximum logo dimensions are 500px wide by 100px high. The uploaded file will be resized to fit within those constraints.\"
          },
          {
            \"type\": \"image_picker\",
            \"id\": \"cart_right_image\",
            \"label\": \"Right image\",
            \"info\": \"Maximum logo dimensions are 500px wide by 100px high. The uploaded file will be resized to fit within those constraints.\"
          }
        ]
      }
      {% endschema %}";

    }

    public function webHook(Request $request){

        $dados = $request->all();
        // Log::write('info', 'retorno do shopify ' . print_r($dados, true) );
        // return 'success';

        $projeto = Projeto::find($request->id_projeto);

        if(!$projeto){
            Log::write('info', 'projeto não encontrado no retorno do shopify, projeto = ' . $request->id_projeto );
            return 'error';
        }
        else{
            Log::write('info', 'retorno do shopify, projeto = ' . $request->id_projeto );
        }

        foreach($dados['variants'] as $variant){

            $plano = Plano::where('shopify_variant_id' , $variant['id'])->first();

            $descricao = '';
            try{
                $descricao = $variant->getOption1();
                if($descricao == 'Default Title'){
                    $descricao = '';
                }
                if($variant->getOption2() != ''){
                    $descricao .= ' - '. $$variant->getOption2();
                }
                if($variant->getOption3() != ''){
                    $descricao .= ' - '. $$variant->getOption3();
                }
            }
            catch(\Exception $e){
                //
            }

            if($plano){
                $plano->update([
                    'nome'      => substr($dados['title'],0,100),
                    'preco'     => $variant['price'],
                    'descricao' => $descricao
                ]);
            }
            else{
                $produto = Produto::create([
                    'user'          => \Auth::user()->id,
                    'nome'          => substr($dados['title'],0,100),
                    'descricao'     => $descricao,
                    'garantia'      => '0',
                    'disponivel'    => true,
                    'quantidade'    => '0',
                    'formato'       => 1,
                    'categoria'     => '1',
                    'custo_produto' => '',
                ]);

                $novoCodigoIdentificador = false;

                while($novoCodigoIdentificador == false){

                    $codigoIdentificador = $this->randString(3).rand(100,999);
                    $plano = Plano::where('cod_identificador', $codigoIdentificador)->first();
                    if($plano == null){
                        $novoCodigoIdentificador = true;
                    }
                }

                $userProjeto = UserProjeto::where([
                    ['user', \Auth::user()->id],
                    ['projeto',$projeto['id']],
                    ['tipo', 'produtor']
                ])->first();

                $plano = Plano::create([
                    'shopify_id'         => $dados['id'],
                    'shopify_variant_id' => $variant['id'],
                    'empresa'            => $userProjeto->empresa,
                    'projeto'            => $projeto['id'],
                    'nome'               => substr($dados['title'],0,100),
                    'descricao'          => $descricao,
                    'cod_identificador'  => $codigoIdentificador, 
                    'preco'              => $variant['price'],
                    'frete_fixo'         => '1',
                    'valor_frete'        => '0.00',
                    'pagamento_cartao'   => true,
                    'pagamento_boleto'   => true,
                    'status'             => '1',
                    'transportadora'     => '2',
                    'qtd_parcelas'       => '12',
                    'parcelas_sem_juros' => '1'
                ]);

                if(count($dados['variants']) > 1){
                  foreach($dados['images'] as $image){

                      foreach($image['variant_ids'] as $variantId){
                          if($variantId == $variant['id']){

                              if($image['src'] != ''){
                                  $produto->update([
                                      'foto' => $image->getSrc()
                                  ]);

                                  $plano->update([
                                      'foto' => $image->getSrc()
                                  ]);
                              }
                              else{
                                  $plano->update([
                                      'foto' => $dados['image']['src']
                                  ]);
                                  $produto->update([
                                      'foto' => $dados['image']['src']
                                  ]);
                              }
                          }
                      }
                  }
              }
              else{

                  $plano->update([
                      'foto' => $dados['image']['src']
                  ]);

                  $produto->update([
                      'foto' => $dados['image']['src']
                  ]);
              }

              ProdutoPlano::create([
                    'produto'            => $produto->id,
                    'plano'              => $plano->id,
                    'quantidade_produto' => '1'
              ]);

            }
        }

        return 'true';
    }

    function randString($size){

        $basic = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $return= "";

        for($count= 0; $size > $count; $count++){

            $return.= $basic[rand(0, strlen($basic) - 1)];
        }

        return $return;
    }

}
