<!DOCTYPE html>

   <head>
      <meta charset="utf-8">
      <title>{{$plano->nome}} - Checkout </title>
      <meta content="width=device-width, initial-scale=1" name="viewport">
      <meta content="Webflow" name="generator">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <link href="{{ asset('webflow-files/css/normalize.css') }}" rel="stylesheet" type="text/css">
      <link href="{{ asset('webflow-files/css/webflow.css') }}" rel="stylesheet" type="text/css">
      <link href="{{ asset('webflow-files/css/pagct.webflow.css') }}" rel="stylesheet" type="text/css">
      <link href="{{ asset('webflow-files/css/style.css') }}" rel="stylesheet" type="text/css">
      <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css">
      <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js" type="text/javascript"></script>
      <script src="{{ asset('webflow-files/js/jquery-3.3.1.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('webflow-files/js/jquery.mask.js') }}" type="text/javascript"></script>
      <script type="text/javascript">WebFont.load({  google: {    families: ["Lato:100,100italic,300,300italic,400,400italic,700,700italic,900,900italic","Montserrat:100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic","Open Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic"]  }});</script>
      <script type="text/javascript">!function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart"in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")}(window,document);</script>

      {!!$layout["multi"]!!}

    </head>
    <body {!!$layout["padrao"]!!}>
       <div class="definebg"></div>

      <div class="conteudoCheckout">
        <div class="loadingdiv" style="display:none;" id="load" >
            <img src="{{ asset('webflow-files/images/load.gif') }}" width="96.5">
        </div>

        <div class="topo">
        <h4>Pré-visualização</h4>
         <img src="{{ $logo }}" class="image-6" alt="Sem logo" style="max-height: 100px">
         <div class="text-block-9 w-hidden-main w-hidden-medium w-hidden-small">
            <span class="awesomespan"></span> COMPRA 100% SEGURA
         </div>
         <div class="bandeiras">
            <img src="{{ asset('webflow-files/images/visa.svg') }}" width="40">
            <img src="{{ asset('webflow-files/images/master.svg') }}" width="40">
            <img src="{{ asset('webflow-files/images/hipercard.svg') }}" width="40">
            <img src="{{ asset('webflow-files/images/elo.svg') }}" width="40">
            <img src="{{ asset('webflow-files/images/diners.svg') }}" width="40">
            <img src="{{ asset('webflow-files/images/amex.svg') }}" width="40">
            <img src="{{ asset('webflow-files/images/boleto.svg') }}" width="40" class="image-7">
            <div class="text-block-9 w-hidden-tiny">
               <span class="awesomespan"></span> COMPRA 100% SEGURA
            </div>
         </div>
      </div>
      <div class="mobilepad">
         <div class="corpo">
            <div class="formulario">
               <div class="formcheckout w-form">
                  <form id="formulario_pagamento" class="form" >
                     <input type="hidden" name="plano" value="{{$plano->cod_identificador}}" >
                     <input type="hidden" id="cod-plano" name="codigo_plano" value="{{ $plano->cod_identificador }}" >
                     <input type="hidden" id="cod-cupom" name="cod-cupom" value="">
                     <input id='cupom' type="hidden">
                     <div class="dadospessoais">
                        <h1 class="tituloform">Identificação</h1>
                        <div class="hr hrform w-embed">
                           <hr style="color: #dfdfdf;">
                        </div>
                        <label for="nome" class="titulofield">NOME COMPLETO</label>
                        <input id="nome" name="nome" type="text" class="fieldpadrao nome w-input" minlength="8" maxlength="100" data-check=""  data-name="nome" placeholder="Ex: Fulano de Tal" >
                        <label for="email" class="titulofield">E-MAIL</label>
                        <input name="email" id="email" type="email" class="fieldpadrao w-input" minlength="8" maxlength="60" data-check="" data-name="email" placeholder="Ex: fulano@gmail.com" >
                        <div class="duascolunas esconderdaddos none ">
                           <div class="div50p">
                              <label for="CPFCNPJ" class="titulofield">CPF</label>
                              <input id="cpf" name="cpfcnpj" type="tel" class="fieldpadrao _2col w-input" minlength="11" maxlength="22" data-check="" data-name="cpfcnpj" placeholder="Ex: 000.000.000-00" >
                           </div>
                           <div class="clearfix"></div>
                           <div class="div50p">
                              <label for="Telefone" class="titulofield">DDD + CELULAR</label>
                              <input id="telefone" name="telefone" type="tel" class="fieldpadrao _2col w-input"  data-check="" data-name="telefone" placeholder="Ex: (00) 0 0000-000" >
                           </div>
                        </div>
                     </div>

                     <div class="dadosentrega ">
                        <h1 class="tituloform">Entrega</h1>
                        <div class="hr hrform w-embed">
                           <hr style="color: #dfdfdf;">
                        </div>
                        <label for="CEP" class="titulofield esconderentrega1 none">CEP</label>
                        <input id="cep" name="cep" type="tel" class="fieldpadrao cep w-input esconderentrega1 none" minlangth="8" maxlength="9" data-check="" data-name="cep" placeholder="Ex: 00000-000" >
                        <label for="Endere-o" class="titulofield esconderentrega2 none">ENDEREÇO</label>
                        <input id="endereco" name="endereco" type="text" class="fieldpadrao w-input esconderentrega2 none" minlangth="6" maxlength="120" data-check="" data-name="endereco" placeholder="Ex: Rua Tal" >
                        <div class="duascolunas esconderentrega2 none">
                           <div class="div50p">
                              <label for="numerocasa" class="titulofield">Nº</label>
                              <input id="numero-casa" name="numero-casa" type="tel" class="fieldpadrao _2col w-input" minlangth="1" maxlength="30" data-check="" data-name="numero-casa" placeholder="EX: 000" >
                           </div>
                           <div class="clearfix"></div>
                           <div class="div50p">
                              <label for="complemento" class="titulofield">COMPLEMENTO</label>
                              <input id="complemento" name="complemento" type="text" class="fieldpadrao _2col w-input" minlangth="6" maxlength="40" data-check=""  placeholder="Ex: Bloco 0 Ap. 2 ">
                           </div>
                        </div>
                        <label for="Bairro" class="titulofield esconderentrega2 none">BAIRRO</label>
                        <input id="bairro" name="bairro" type="text" class="fieldpadrao _2col w-input esconderentrega2 none" minlangth="6" maxlength="40" data-check="" data-name="bairro" placeholder="Ex: Centro" >
                        <div class="duascolunas mob-h esconderentrega2 none">
                           <div class="div50p">
                              <label for="Cidade" class="titulofield">CIDADE</label>
                              <input id="cidade" name="cidade" type="text" class="fieldpadrao _2col w-input" maxlength="256" name="Cidade" data-check="" data-name="Cidade" placeholder="Ex: Cidade" id="Cidade" >
                           </div>
                           <div class="clearfix"></div>
                           <div class="div50p">
                              <label for="estado" class="titulofield">ESTADO</label>
                                <select id="estado" name="estado" data-check="" required="" class="fieldpadrao w-select">
                                    <option value="sel" selected>Selecione</option>
                                </select>
                           </div>
                        </div>
                     </div>

                     <div class="dadospagamento ">
                        <h1 class="tituloform">Pagamento</h1>
                        <hr style="color: #dfdfdf;" class="esconderinfopagamento1 none">
                        <div data-duration-in="300" data-duration-out="100" data-easing="linear" class="w-tabs esconderinfopagamento1 none">
                           <div class="tabs-menu w-tab-menu ">
                              <a data-w-tab="Cartão de Crédito" id="pagar-cartao" class="tab-link-tab-1 roxo w-inline-block w-tab-link w--current">
                                 <div class="tipopag ">
                                    <span class="text-span-2"></span>CRÉDITO
                                 </div>
                              </a>
                              <a data-w-tab="Boleto Bancário" id="pagar-boleto" class="tab-link-tab-2 roxo w-inline-block w-tab-link">
                                 <div class="tipopag boleto">
                                    <span class="text-span-2"></span> BOLETO
                                 </div>
                              </a>
                           </div>
                           <div class="tabs-content w-tab-content ">
                              <div data-w-tab="Cartão de Crédito" class="w-tab-pane w--tab-active">
                                 <div class="pagcartao">
                                    <div class="trazside"></div>
                                    <div data-duration-in="300" data-duration-out="100" class="w-tabs">
                                       <div class="w-tab-content">
                                          <div data-w-tab="Tab 2" class="w-tab-pane"></div>
                                          <div data-w-tab="Tab 3" class="w-tab-pane"></div>
                                       </div>
                                    </div>
                                    <label for="cartao" class="titulofield ">NÚMERO DO CARTÃO</label>
                                    <input id="cartao" data-checkout="cardNumber" data-pag="pagamento"  type="tel" class="fieldpadrao bandeirascard w-input " minlength="16" maxlength="20" data-check="" data-name="cartao" placeholder="Ex: 0000 0000 0000 0000" required=""  >
                                    <label for="nome-cartao" class="titulofield">NOME IMPRESSO NO CARTÃO</label>
                                    <input id="nome-cartao" data-checkout="cardholderName" data-pag="pagamento" type="text" class="fieldpadrao w-input" minlength="8" maxlength="100" data-check="" data-name="nome-cartao" placeholder="Ex: Fulano de Tal" required="">
                                    <label for="CPFCartao" class="titulofield esconderinfopagamento3 none">CPF DO TITULAR DO CARTÃO</label>
                                    <input id="cpf-cartao" data-checkout="docNumber"  data-pag="pagamento" type="tel" class="fieldpadrao w-input esconderinfopagamento3 none" minlength="8" maxlength="15" data-check="" data-name="nome-cartao" placeholder="Ex: 000.000.000.00" required="">
                                    <div class="duascolunas _2colcartao esconderinfopagamento3 none">
                                       <div id="data-cartao" class="div50p">
                                          <label for="validade" class="titulofield">VALIDADE</label>
                                          <div class="selectvalidade">
                                             <select id="validade" data-checkout="cardExpirationMonth" data-check="" data-name="validade" data-pag="pagamento" class="fieldpadrao _50 w-select" >
                                                <option value="sel" selected>Mês</option>
                                             </select>
                                             <select id="ano-validade" data-checkout="cardExpirationYear" data-check="" data-name="ano-validade" data-pag="pagamento" class="fieldpadrao _50 w-select" >
                                                <option value="sel" selected>Ano</option>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="clearfix"></div>
                                       <div id="cvv" class="div50p">
                                          <label for="codigo-seguranca" class="titulofield cvv">COD. SEGURANÇA</label>
                                          <input id="codidgo-seguranca" id="codSeguranca" data-checkout="securityCode" data-check="" data-pag="pagamento" type="tel" class="fieldpadrao _2col cvv w-input"  data-name="codSeguranca" placeholder="CVV" >
                                       </div>
                                    </div>
                                    <input id="qtd_parcelas" type="hidden" name="qtd_parcelas" >
                                    <label for="parcelas" class="titulofield cvv esconderinfopagamento3 none">PARCELAMENTO</label>
                                    <select id="parcelas" name="parcelas" data-name="parcelas" data-check="" data-pag="pagamento"  class="fieldpadrao selectparcelas w-select esconderinfopagamento3 none" required="">
                                       <option value="">Selecione</option>
                                    </select>
                                 </div>
                                 <div class="btnsfinal">
                                    <button value="FINALIZAR COMPRA" data-wait="Processando pagamento..." data-pag="pagamento" class="btnpadrao w-button {{ $botoes }}">
                                        FINALIZAR COMPRA
                                    </button>
                                 </div>
                              </div>
                              <div data-w-tab="Boleto Bancário" class="w-tab-pane">
                                 <div class="pagboleto">
                                     @if($plano->desconto == "boleto")
                                     <div class="alert alert-boleto">
                                            <strong>{{$plano->mensagen_desconto}}</strong> 
                                          </div>
                                     @endif
                                    <div class="text-block-8">• Você precisa pagar seu boleto antes do vencimento para garantir o envio do seu pedido;<br>
                                       <br>• Não é possível parcelar o valor da compra no boleto bancário;<br>
                                       <br>• Seu boleto vence em 3 dias úteis, não deixe para pagar depois;<br>
                                       <br>• Se você quiser imprimir seu boleto, use o código de barras no caixa eletrônico para efetuar o pagamento;<br>
                                       <br>• O boleto não será enviado para o endereço de entrega.
                                    </div>
                                    <button  id="bt_gerar_boleto" class="btnpadrao btnboleto w-button gerar-boleto {{ $botoes }}" data-pag="pagamento">GERAR BOLETO</button>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="hr hrfooter w-embed">
                        <hr style="color: #dfdfdf;">
                     </div>
                  </form>
                  <div class="success-message w-form-done">
                     <div class="text-block-14"><strong>Seu pedido foi aprovado.<br>‍<br>
                        </strong>Você receberá uma confirmação em seu e-mail em breve.<br>Estamos redirecionando você para a página de confirmação!
                     </div>
                  </div>
               </div>
            </div>
            <div class="infopedido">
               <div class="infocarrinho">
                  <div class="topo-infocarrinho">
                     <h1 class="titulocarrinho">Seu pedido</h1>
                  </div>
                  <div class="hr w-embed">
                     <hr style="color: #dfdfdf;">
                  </div>
                  <div class="addproduto">
                     <div class="produtocarrinho">
                        <div class="imgcarrinho p01">
                           <img src="{{ asset($foto) }}"  >
                        </div>
                        <div class="descricaocarrinho">
                           <div class="tit-nomeproduto">{{$plano->nome}}</div>
                           <div class="preco-produto">R${{$plano->preco}}</div>
                        </div>
                     </div>
                     <div class="hr w-embed">
                        <hr style="color: #dfdfdf;">
                     </div>
                  </div>
                  <div class="formcupom w-form">
                     <!--id="email-form-2"-->
                     <form id="formulario_cupom"  name="email-form-2" data-name="Email Form 2" method="POST">
                        <label for="name-4" class="titulofield">TEM UM CUPOM DE DESCONTO?</label>
                        <div class="divcupom">
                           <input type="text" class="fieldpadrao fieldcupom w-input" minlength="4" maxlength="40" name="CupomdeDesconto" data-name="CupomdeDesconto" placeholder="Insira seu cupom" id="CupomdeDesconto" required="">
                           <div class="clearfix"></div>
                           <input type="submit" value="ATIVAR" class="btncupom w-button {{ $botoes != null ? $botoes : 'btnpadrao' }}">
                        </div>
                     </form>
                  </div>
                  <div class="valoresfinais">
                     <div class="sub-entrega">
                        <div class="p-padrao infos">Subtotal</div>
                        <div class="valor valor-subtotal" id="subtotal" >R$ {{$plano->preco}}</div>
                     </div>
                     <div id="desconto" class="sub-entrega"></div>
                     <div class="sub-entrega">
                        <div class="p-padrao infos">Entrega</div>
                        <div class="valor valor-entrega" id="valor-entrega">-</div>
                     </div>
                     <div class="hr w-embed">
                        <hr style="color: ##dfdfdf;">
                     </div>
                     <div class="total">
                        <div class="tit-totla">Total</div>
                        <div id="valor-total" class="valortotal" valor-total="{{$plano->preco}}">R$ {{$plano->preco}}</div>
                     </div>
                  </div>
               </div>
               <div class="infoadicionais w-hidden-medium w-hidden-small w-hidden-tiny">
                  <div class="baseinfoad borderpad roxo"  id="frete">
                     <div class="row-ia w-row">
                        <div class="col1 w-col w-col-4">
                           <h1 class="icon-infoadicional awesomespan"></h1>
                        </div>
                        <div class="col2 w-col w-col-8">
                           <h5 class="tit-infoadicional">Prazo de Entrega</h5>
                           <div id="msg-frete" class="desc-infoadicional"></div>
                        </div>
                     </div>
                  </div>
                  <div class="baseinfoad semborda semfundo">
                     <div class="row-ia w-row">
                        <div class="w-col w-col-4">
                           <h1 class="icon-infoadicional awesomespan"></h1>
                        </div>
                        <div class="w-col w-col-8">
                           <h5 class="tit-infoadicional">Televendas</h5>
                           <div class="desc-infoadicional">4020-3302</div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <a href="#" class="mostraeesconde w-hidden-main w-inline-block" data-ix="mostra-e-esconde-infopedido">
               <div class="text-block-12"><span class="awesomespan"></span> Resumo da compra <span class="awesomespan margin"></span></div>
               <div class="text-block-11" id="valor-total1" >R$ {{$plano->preco}}</div>
            </a>
         </div>
      </div>
      <div class="formfooter">
         <img src="{{ asset('webflow-files/images/cloudlogo.png') }}" width="120" class="image-8">
         <div>
            <a id="termos_de_uso" href="/termos" target="_blank" class="link">Termos de Uso</a>
            <a id="politica_privacidade" href="/politicas" target="_blank" class="link">Políticas de Privacidade</a>
         </div>
      </div>
   </div>

    </body>

    <script src="{{ asset('webflow-files/js/webflow.js') }}" type="text/javascript"></script>

    <script>

        $(document).ready(function(){

            $('input').prop('disabled',true);
            $('button').prop('disabled',true);
        });

    </script>

</html>
