<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Checkout</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

	<!-- Icons -->
	<script src="{{ asset('assets/js/feather.min.js') }}"></script>
	
	<!-- Estilos -->
	<link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
	<link rel="stylesheet" href="{{ asset('/assets/css/accordion.css') }}">
	<link rel='stylesheet' href="{{ asset('/assets/css/sweetalert2.min.css') }}">

    <!-- Estilos -->
    <link rel="stylesheet" href="{{ asset('/assets/css/accordion.css') }}"> 
    <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">

</head>

<body>

    <!-- TOPBAR -->
    <div id="topbar" class="topbar align-items-center">
        <div class="container d-flex justify-content-center">
            <div class="wrap">

                <div class="colunas-topo row-no-margin align-items-center justify-content-between">

                    <div class="col-xs-4">
                        <img src="{{ $logo }}" alt="Logo" style="height: 60px">
                    </div>

                    <div class="col-xs-4 align-items-center certificado text-center verde">
                        <i data-feather="lock" class="align-middle"></i>
                        <span class="align-middle">100% Seguro </span>
                    </div>

                    <div class="col-xs-4 text-right">

                        <button id="abrirTopbar" class="btn btn-outline-primary botao-topbar" type="button">

                            <i data-feather="shopping-cart" class="align-middle"></i>
                            <span id="preco-botao" class="hidden-m"> | R$ <span class="valor_total">{!! $plano->preco !!}</span></span>
                            <i id="btn-chevron" data-feather="chevron-right" class="chevron-down align-middle"></i>

                        </button>

                        <button id="fecharTopbar" class="btn btn-outline-primary botao-topbar" type="button">

                            <i data-feather="x" class="align-middle"></i>
                            <span id="preco-botao" class="hidden-m"> Fechar </span>

                        </button>

                    </div>

                </div>

                <!-- DETALHES DO PEDIDO -->
                <div id="detalhes" class="detalhesPedido justify-content-center">

                    <div class="holderProdutos">

                        <div class="row-no-margin d-flex align-items-center justify-content-xs-center justify-content-sm-center justify-content-md-between listaPedido">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 centralizar">
                                <img src="{{ $foto }}" width="110px;">
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6  centralizar">
                                <p> <strong> {!! $plano->nome !!} </strong>
                                    <br>
                                    <span class="descProduto hidden-m"> {!! $plano->descricao !!} </span>
                                </p>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 text-center">
                                <h5> <strong> R$ {!! $plano->preco !!}</strong> </h5>
                            </div>
                        </div>

                    </div>

                    <div class="holderValores row-no-margin justify-content-center align-items-center">
                        <div class="col-xs-12 col-md-3 col-lg-3 text-center">
                            <div class=""><span> <strong> Subtotal: </strong></span>R$ <span id="valor_plano">{!! $plano->preco !!}</span></div>
                        </div>
                        <div class="col-xs-12 col-md-3 col-lg-3 text-center">
                            <div class=""> <span> <strong> Desconto: </strong></span> R$ <span id="valor_desconto">0.00</span></div>
                        </div>
                        <div class="col-xs-12 col-md-3 col-lg-3 text-center">
                            <div class=""> <span> <strong> Entrega: </strong></span> R$ <span id="valor_frete"> 0.00 </span></div>
                        </div>
                        <div class="col-xs-12 col-md-3 col-lg-3">
                            <div class="total-badge d-flex justify-content-between"> 
                                <span><strong> Total: R$ </strong></span> 
                                <strong> <span class="valor_total"> {!! $plano->preco !!}</span> </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- INÍCIO CONTEÚDO DE FORMULÁRIO E EDIÇÕES DO USUÁRIO NO CHECKOUT  -->
    <form id="formulario_pagamento">

        {{ csrf_field() }}
        <div id="corpo">

            <div class="container d-flex justify-content-center">
                <div class="imagem-produto text-center">
                    <!-- <img src="{{ asset('assets/img/capa.png') }}"> -->
                </div>
            </div>

            <div class="container d-flex justify-content-center">

                <div id="corpoForm">

                    <div class="row-no-margin justify-content-center">

                        <!-- FORMULÁRIO DE COMPRA -->
                        <div class="col-12">
                            <input type="hidden" name="cod_identificador" value="{!! $plano->cod_identificador !!}">

                            <!-- SESSÃO DE DADOS PESSOAIS DO CLIENTE -->
                            <div id="dados_pessoais" class="secao-form">
                                <div id="holderDadosPessoais">
                                    <h5 class="titulo-sessao"> <strong> 1. Seus dados </strong></h5>

                                    <!-- INPUTS E-MAIL E TELEFONE -->
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="input-group input-group-icon invalido">
                                                <input name="email" id="email" type="text" placeholder="Seu e-mail" disabled/>
                                                <div class="input-icon" style="vertical-align:middle"> <i data-feather="mail" class="icon-center"></i>
                                                </div>
                                                <div class="invalid-feedback">
                                                    Email inválido. Ex: renato@gmail.com
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="input-group input-group-icon">
                                                <input name="telefone" id="telefone" type="tel" data-mask="(00) 00000-0000" placeholder="DDD e Telefone" disabled/>
                                                <div class="input-icon"> <i data-feather="phone" class="icon-center"></i>
                                                </div>
                                                <div class="invalid-feedback">
                                                    Telefone inválido. Ex: (51) 996104598
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- INPUTS NOME E CPF -->
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
                                            <div class="form-group input-group input-group-icon">
                                                <input name="nome" type="text" id="nome" placeholder="Seu nome completo" disabled />
                                                <div class="input-icon"> <i data-feather="user" class="icon-center"></i>
                                                </div>
                                                <div class="invalid-feedback">
                                                    Nome inválido. Ex: Renato Silva
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
                                            <div class="input-group input-group-icon">
                                                <input name="cpf" id="cpf" type="tel" data-mask="000.000.000-00" placeholder="Seu CPF"  disabled/>
                                                <div class="input-icon"> 
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="icon-center"><path d="M19.391 17.097c.351.113.526.498.385.833-.135.319-.495.482-.827.374-2.617-.855-4.357-2.074-5.285-3.693-.177-.308-.068-.7.243-.875.311-.175.707-.067.883.241.77 1.341 2.285 2.372 4.601 3.12zm-8.59-.611c-.849.491-2.271 1.315-5.227 2.186-.41.121-.597.591-.382.956.124.229.422.372.717.285 2.703-.793 4.203-1.557 5.142-2.087.933-.526 1.02-.535 1.904.11.856.626 2.31 1.537 4.894 2.477.296.107.611-.025.747-.249.229-.35.071-.821-.324-.965-3.083-1.124-4.426-2.186-5.094-2.715-.866-.685-1.156-.705-2.377.002zm-.263 3.068c-.638.328-1.6.822-3.251 1.393-.215.074-.375.252-.425.472-.108.475.343.915.79.762 1.772-.607 2.803-1.138 3.482-1.487.518-.267.835-.321 1.429-.001.752.404 1.938 1.042 3.593 1.705.468.188.945-.226.856-.714-.04-.221-.191-.405-.401-.49-1.578-.635-2.711-1.244-3.431-1.631-1.133-.609-1.265-.717-2.642-.009zm-.694 3.25c-.228.106-.369.337-.358.586.017.387.368.61.693.61.091 0 .181-.018.26-.055 1.7-.792 1.11-.84 3.027.005.076.034.161.05.25.05.32 0 .677-.212.698-.603.014-.256-.134-.493-.37-.597-2.496-1.095-1.827-1.096-4.2.004zm2.354-14.206c.139-.327-.017-.704-.346-.841-.33-.137-.709.016-.848.343-1.058 2.498-3.731 4.424-7.253 5.273-.335.081-.551.404-.495.741.06.361.417.598.78.511 3.469-.833 6.784-2.773 8.162-6.027zm.647 4.136c.822-.932 1.476-1.965 1.944-3.071.47-1.111.389-2.231-.228-3.153-.646-.964-1.815-1.563-3.051-1.563-.698 0-1.37.192-1.944.555-.627.398-1.122.995-1.432 1.726-.647 1.527-2.344 2.755-4.654 3.411-.288.082-.485.345-.48.643.007.416.41.711.813.597 2.7-.766 4.714-2.263 5.515-4.153.444-1.05 1.322-1.494 2.182-1.494 1.428 0 2.81 1.224 2.085 2.935-1.529 3.612-5.11 5.937-9.157 6.958-.178.045-.33.162-.417.323-.087.161-.104.351-.044.523.107.31.436.485.755.405 1.984-.499 3.819-1.28 5.372-2.294 1.048-.685 1.97-1.474 2.741-2.348zm-5.819-6.2c.293-.501.571-.974 1.049-1.414 1.13-1.041 2.662-1.543 4.204-1.379 1.453.155 2.734.882 3.514 1.993 1.08 1.539.809 3.067.547 4.544-.225 1.263-.456 2.569.263 3.712.543.863 1.571 1.518 3.177 2.006.339.103.699-.098.785-.439.087-.345-.113-.696-.456-.802-1.246-.382-2.04-.86-2.407-1.444-.457-.726-.285-1.691-.087-2.81.279-1.571.625-3.526-.759-5.5-.994-1.417-2.611-2.341-4.438-2.536-1.914-.205-3.818.42-5.223 1.715-.62.569-.975 1.174-1.288 1.708-.493.84-.909 1.546-2.312 2.005-.222.073-.398.261-.435.54-.06.46.386.827.832.682 1.879-.614 2.464-1.611 3.034-2.581zm-2.06-1.69l.387-.572c1.549-2.217 4.286-3.304 7.323-2.909 2.886.376 5.256 2.014 6.037 4.173.692 1.914.419 3.459.199 4.701-.19 1.072-.354 1.999.22 2.742.233.302.565.535 1.021.71.38.146.796-.105.842-.505.035-.302-.137-.59-.42-.707-.195-.08-.334-.173-.416-.279-.229-.295-.116-.933.027-1.739.233-1.318.553-3.123-.255-5.357-1.13-3.123-4.746-5.102-8.454-5.102-2.466 0-4.86.882-6.553 2.746-.427.478-.69.823-.945 1.409-.167.382-.102.658.178.848.275.187.627.115.809-.159z"/></svg>
                                                </div>
                                                <div class="invalid-feedback">
                                                    CPF inválido. Ex: 326.947.280-30
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SESSÃO DE DADOS DE ENTREGA DO CLIENTE (SE FOR PRODUTO FÍSICO) -->
                            <div id="dados_entrega" class="secao-form">

                                <!-- ESSA PARTE DA ENTREGA (HOLDER ENTREGA) É O QUE APARECERÁ JUNTO COM OS DADOS PESSOAIS NA PRIMEIRA VIEW DO USUÁRIO -->
                                <div id="holderEntrega">
                                    <h5 class="titulo-sessao"> <strong> 2. Entrega </strong> </h5>
                                    <div class="row d-flex align-items-center">
                                        <div class="col-xs-12 col-sm-6 col-md-5 col-lg-5">
                                            <div class="input-group input-group-icon">
                                                <input name="cep" id="cep" type="tel" data-mask="00000-000" placeholder="Seu CEP" disabled/>
                                                <div class="input-icon"> 
                                                    <i data-feather="home" class="icon-center"></i>
                                                </div>
                                                <div class="invalid-feedback">
                                                    CEP inválido. Ex: 96400600
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4" style="margin-bottom: 15px;">
                                            <a href="http://www.buscacep.correios.com.br/sistemas/buscacep/" target="_blank" disabled> 
                                                Não sei meu CEP 
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div id="dados_adicionais_entrega">

                                    <!-- INPUTS ENDEREÇO E NÚMERO  -->
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                            <div class="input-group">
                                                <input name="rua" id="rua" type="text" placeholder="Rua" disabled/>
                                                <div class="invalid-feedback">
                                                    Rua inválida. Ex: Avenida Brasil
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                            <div class="input-group">
                                                <input name="numero" id="numero" type="tel" data-mask="0#" placeholder="Número" disabled/>
                                                <div class="invalid-feedback">
                                                    Número inválido. Ex: 1234
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- INPUTS COMPLEMENTO, BAIRRO, CIDADE E ESTADO + RADIO FRETE -->
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6 col-md-3 col-lg3">
                                            <div class="input-group">
                                                <input name="ponto_referencia" id="ponto_referencia" type="text" placeholder="Complemento" disabled/>
                                            </div>
                                        </div>

                                        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <div class="input-group">
                                                <input name="bairro" id="bairro" type="text" placeholder="Bairro" disabled/>
                                                <div class="invalid-feedback">
                                                    Bairro inválido. Ex: Centro
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <div class="input-group">
                                                <input name="cidade" id="cidade" type="text" placeholder="Cidade" disabled/>
                                                <div class="invalid-feedback">
                                                    Cidade inválida. Ex: Gramado
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <div class="input-group">
                                                <select name="estado" id="estado"  disabled>
                                                    <option value="">Selecione</option>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Estado inválido. Ex: Minas Gerais
                                                </div>
                                            </div>
                                        </div>

                                        
                                    </div>
                                </div>                                
                                
                            </div>
                        </div>
                        <!-- SELEÇÃO DO TIPO DE FRETE -->
                        <div id="selecao_frete" class="row text-center col-12" style="display: none">
                            <!-- pac -->
                            <div class="col-xs-12 col-sm-12 {!! $plano->frete_fixo == 0 ? 'col-md-6' : '' !!}">
                                <label class="radio d-flex">
                                    <div class="row full align-items-center justify-content-between">
                                        <div class="col-2 text-center">
                                            <input id="entrega_pac" class="custom-radio" name="tipo_entrega" type="radio" value="pac" style="width: 20px; height: 20px;" checked>
                                        </div>
                                        <div class="col-6">
                                            <span class="titulo-radio"> 
                                                    <strong>
                                                        PAC
                                                    </strong>
                                                </span>
                                            <p class="descricao-radio" id="prazo_pac">
                                                <!-- CARREGADO DE ACORDO COM O CEP -->
                                            </p>
                                        </div>
                                        <div class="col-4 text-right">
                                            <span class="titulo-radio">
                                                <!-- CARREGADO DE ACORDO COM O CEP --> 
                                                <strong id="preco_pac"></strong> 
                                            </span>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            @if(!$plano->frete_fixo)
                                <!-- sedex -->
                                <div class="col-xs-12 col-sm-12 col-md-6">
                                    <label class="radio d-flex">

                                        <div class="row full align-items-center justify-content-between">

                                            <div class="col-2 text-center">
                                                <input id="entrega_sedex" class="custom-radio" name="tipo_entrega" value="sedex" type="radio" style="width: 20px; height: 20px;">
                                            </div>

                                            <div class="col-6">
                                                <span class="titulo-radio"> 
                                                        <strong> 
                                                            SEDEX 
                                                        </strong>
                                                    </span>
                                                <p class="descricao-radio" id="prazo_sedex">
                                                    <!-- CARREGADO DE ACORDO COM O CEP -->
                                                </p>
                                            </div>

                                            <div class="col-4 text-right">
                                                <span class="titulo-radio">
                                                    <!-- CARREGADO DE ACORDO COM O CEP -->
                                                    <strong id="preco_sedex"></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif
                        </div>

                        <!-- MODAL DE CUPOM DE DESCONTO -->
                        <div class="modal fade" id="modalCupom" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">

                                    <div class="modal-header cupom align-items-center">
                                        <h5 class="text-center">
                                            <strong> Cupom de Desconto </strong>
                                        </h5>

                                        <button id="fechar_modal_desconto" type="button" class="close" style="color: white;" data-dismiss="modal" aria-label="Close" disabled>
                                            <span aria-hidden="true">&times;</span>
                                        </button>

                                    </div>

                                    <div class="modal-body cupom d-flex align-items-center justify-content-center">
                                        <div class="row">
                                            <div class="col-9" style="padding:0;">
                                                <div class="input-group input-group-icon input-cupom" style="margin:0;">
                                                    <input id="cupom" name="cupom_desconto" class="input-cupom" type="text" placeholder="Digite seu cupom" disabled/>
                                                    <div class="input-icon"> <i data-feather="dollar-sign" class="align-middle icon-center"></i> </div>
                                                </div>
                                            </div>
                                            <div class="col-3" style="padding:0">
                                                <button id="bt_adicionar_cupom" class="btn btn-outline-primary btn-cupom" style="margin:0;height: 57px" disabled>
                                                    <p id="label_bt_adicionar_cupom">Inserir</p>
                                                    <div id="loading_validado_cupom" class="validando" style="padding:0; display: none">
                                                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="24px" height="15px" viewBox="0 0 24 30" style="enable-background:new 0 0 50 50;" xml:space="preserve">
                                                            <rect x="0" y="5.04731" width="4" height="20.9054" fill="#333">
                                                                <animate attributeName="height" attributeType="XML" values="5;21;5" begin="0s" dur="0.6s" repeatCount="indefinite"></animate>
                                                                <animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0s" dur="0.6s" repeatCount="indefinite"></animate>
                                                            </rect>
                                                            <rect x="10" y="8.95269" width="4" height="13.0946" fill="#333">
                                                                <animate attributeName="height" attributeType="XML" values="5;21;5" begin="0.15s" dur="0.6s" repeatCount="indefinite"></animate>
                                                                <animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0.15s" dur="0.6s" repeatCount="indefinite"></animate>
                                                            </rect>
                                                            <rect x="20" y="12.9527" width="4" height="5.09461" fill="#333">
                                                                <animate attributeName="height" attributeType="XML" values="5;21;5" begin="0.3s" dur="0.6s" repeatCount="indefinite"></animate>
                                                                <animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0.3s" dur="0.6s" repeatCount="indefinite"></animate>
                                                            </rect>
                                                        </svg>
                                                    </div>
                                                </button>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- SESSÃO DE PAGAMENTO -->
                    <div id="pagamento" class="col-12">

                        <div class="secao-form">
                            <h5 class="titulo-sessao"> <strong><label id="etapa_pagamento">3</label>. Pagamento </strong> </h5>
                        </div>
                        <!-- ACCORDION COM OPÇÕES DE PAGAMENTO  -->
                        <ul id="accordion" class="accordion">

                            <!-- PAGAMENTOS EM CARTÃO DE CRÉDITO -->
                            <li class="default open">
                                <div class="link" id="accordion_cartao">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" class="icone-tab align-middle">
                                        <path d="M21.5 6c.276 0 .5.224.5.5v11c0 .276-.224.5-.5.5h-19c-.276 0-.5-.224-.5-.5v-11c0-.276.224-.5.5-.5h19zm2.5 0c0-1.104-.896-2-2-2h-20c-1.104 0-2 .896-2 2v12c0 1.104.896 2 2 2h20c1.104 0 2-.896 2-2v-12zm-20 3.78c0-.431.349-.78.78-.78h.427v1.125h-1.207v-.345zm0 .764h1.208v.968h-1.208v-.968zm0 1.388h1.208v1.068h-.428c-.431 0-.78-.349-.78-.78v-.288zm4 .288c0 .431-.349.78-.78.78h-.429v-1.068h1.209v.288zm0-.708h-1.209v-.968h1.209v.968zm0-1.387h-1.629v2.875h-.744v-4h1.593c.431 0 .78.349.78.78v.345zm5.5 2.875c-1.381 0-2.5-1.119-2.5-2.5s1.119-2.5 2.5-2.5c.484 0 .937.138 1.32.377-.53.552-.856 1.3-.856 2.123 0 .824.326 1.571.856 2.123-.383.239-.836.377-1.32.377zm1.5-2.5c0-1.381 1.12-2.5 2.5-2.5 1.381 0 2.5 1.119 2.5 2.5s-1.119 2.5-2.5 2.5c-1.38 0-2.5-1.119-2.5-2.5zm-8 4.5h-3v1h3v-1zm4 0h-3v1h3v-1zm5 0h-3v1h3v-1zm4 0h-3v1h3v-1z"/>
                                    </svg>                                
                                    <strong> Cartão de Crédito </strong> <i data-feather="chevron-right" class="chevron-down align-middle"></i>
                                </div>

                                <div class="tabpag" id="pagCartao">

                                    <div class="row d-flex align-items-center r-reverse-m justify-content-md-between justify-content-lg-between">

                                        <!-- INPUTS INFORMACOES DO CARTAO -->
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="input-group input-group-icon">
                                                        <input type="tel" id="card-number" data-mask="0000 0000 0000 0000" maxlength="20" placeholder="Número do Cartão" disabled/>
                                                        <div class="input-icon"> <i data-feather="credit-card" class="icon-center"></i>
                                                        </div>
                                                        <div class="invalid-feedback">
                                                            Cartão inválido. Ex: 1234 5678 9012 3456
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="input-group input-group-icon">
                                                        <input type="text" id="card-name" name="card_name" placeholder="Nome descrito no cartão" disabled/>
                                                        <div class="input-icon"> <i data-feather="user" class="icon-center"></i>
                                                        </div>
                                                        <div class="invalid-feedback">
                                                            Nome inválido. Ex: Renato Silva
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="input-group input-group-icon">
                                                        <input type="tel" id="card-cpf" name="card_cpf" data-mask="000.000.000-00" placeholder="CPF do titular do cartão" disabled/>
                                                        <div class="input-icon"> 
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="icon-center"><path d="M19.391 17.097c.351.113.526.498.385.833-.135.319-.495.482-.827.374-2.617-.855-4.357-2.074-5.285-3.693-.177-.308-.068-.7.243-.875.311-.175.707-.067.883.241.77 1.341 2.285 2.372 4.601 3.12zm-8.59-.611c-.849.491-2.271 1.315-5.227 2.186-.41.121-.597.591-.382.956.124.229.422.372.717.285 2.703-.793 4.203-1.557 5.142-2.087.933-.526 1.02-.535 1.904.11.856.626 2.31 1.537 4.894 2.477.296.107.611-.025.747-.249.229-.35.071-.821-.324-.965-3.083-1.124-4.426-2.186-5.094-2.715-.866-.685-1.156-.705-2.377.002zm-.263 3.068c-.638.328-1.6.822-3.251 1.393-.215.074-.375.252-.425.472-.108.475.343.915.79.762 1.772-.607 2.803-1.138 3.482-1.487.518-.267.835-.321 1.429-.001.752.404 1.938 1.042 3.593 1.705.468.188.945-.226.856-.714-.04-.221-.191-.405-.401-.49-1.578-.635-2.711-1.244-3.431-1.631-1.133-.609-1.265-.717-2.642-.009zm-.694 3.25c-.228.106-.369.337-.358.586.017.387.368.61.693.61.091 0 .181-.018.26-.055 1.7-.792 1.11-.84 3.027.005.076.034.161.05.25.05.32 0 .677-.212.698-.603.014-.256-.134-.493-.37-.597-2.496-1.095-1.827-1.096-4.2.004zm2.354-14.206c.139-.327-.017-.704-.346-.841-.33-.137-.709.016-.848.343-1.058 2.498-3.731 4.424-7.253 5.273-.335.081-.551.404-.495.741.06.361.417.598.78.511 3.469-.833 6.784-2.773 8.162-6.027zm.647 4.136c.822-.932 1.476-1.965 1.944-3.071.47-1.111.389-2.231-.228-3.153-.646-.964-1.815-1.563-3.051-1.563-.698 0-1.37.192-1.944.555-.627.398-1.122.995-1.432 1.726-.647 1.527-2.344 2.755-4.654 3.411-.288.082-.485.345-.48.643.007.416.41.711.813.597 2.7-.766 4.714-2.263 5.515-4.153.444-1.05 1.322-1.494 2.182-1.494 1.428 0 2.81 1.224 2.085 2.935-1.529 3.612-5.11 5.937-9.157 6.958-.178.045-.33.162-.417.323-.087.161-.104.351-.044.523.107.31.436.485.755.405 1.984-.499 3.819-1.28 5.372-2.294 1.048-.685 1.97-1.474 2.741-2.348zm-5.819-6.2c.293-.501.571-.974 1.049-1.414 1.13-1.041 2.662-1.543 4.204-1.379 1.453.155 2.734.882 3.514 1.993 1.08 1.539.809 3.067.547 4.544-.225 1.263-.456 2.569.263 3.712.543.863 1.571 1.518 3.177 2.006.339.103.699-.098.785-.439.087-.345-.113-.696-.456-.802-1.246-.382-2.04-.86-2.407-1.444-.457-.726-.285-1.691-.087-2.81.279-1.571.625-3.526-.759-5.5-.994-1.417-2.611-2.341-4.438-2.536-1.914-.205-3.818.42-5.223 1.715-.62.569-.975 1.174-1.288 1.708-.493.84-.909 1.546-2.312 2.005-.222.073-.398.261-.435.54-.06.46.386.827.832.682 1.879-.614 2.464-1.611 3.034-2.581zm-2.06-1.69l.387-.572c1.549-2.217 4.286-3.304 7.323-2.909 2.886.376 5.256 2.014 6.037 4.173.692 1.914.419 3.459.199 4.701-.19 1.072-.354 1.999.22 2.742.233.302.565.535 1.021.71.38.146.796-.105.842-.505.035-.302-.137-.59-.42-.707-.195-.08-.334-.173-.416-.279-.229-.295-.116-.933.027-1.739.233-1.318.553-3.123-.255-5.357-1.13-3.123-4.746-5.102-8.454-5.102-2.466 0-4.86.882-6.553 2.746-.427.478-.69.823-.945 1.409-.167.382-.102.658.178.848.275.187.627.115.809-.159z"/></svg>
                                                        </div>
                                                        <div class="invalid-feedback">
                                                            CPF inválido. Ex: 326.947.280-30
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-md-7">
                                                    <div class="input-group input-group-icon">
                                                        <input type="tel" id="card-expiration" data-mask="00/0000" maxlength="7" placeholder="Validade" disabled/>
                                                        <div class="input-icon"> <i data-feather="calendar" class="icon-center"></i>
                                                        </div>
                                                        <div class="invalid-feedback">
                                                            Validade inválida. Ex: 10/24
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-md-5">
                                                    <div class="input-group input-group-icon">
                                                        <input type="tel" id="card-cvv" data-mask="000#" maxlength="4" placeholder="Código de segurança" disabled/>
                                                        <div class="input-icon"> <i data-feather="lock" class="icon-center"></i>
                                                        </div>
                                                        <div class="invalid-feedback">
                                                            Código de segurança inválido. Ex: 123
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 hidden-d">
                                                    <div class="input-group">
                                                        <select id="select_parcelas_mobile" disabled>
                                                            <option value="">selecione</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- CARTÃO DE CRÉDITO INTERATIVO -->
                                        <div class="col-xs-12 col-md-6  d-flex justify-content-center align-items-center">
                                            <div class="row justify-content-center">

                                                <div class="credit-card-box">
                                                    <div class="flip">
                                                        <div class="front">
                                                            <div class="chip"></div>
                                                            <div class="logo" id="cartao_logo">
                                                                <!-- IMAGEM CARREGADA COM JAVASCRIPT -->
                                                            </div>
                                                            <div class="number">
                                                                <div></div>
                                                            </div>
                                                            <div class="card-holder">
                                                                <label>TITULAR</label>
                                                                <div></div>
                                                            </div>
                                                            <div class="card-expiration-date">
                                                                <label>VALIDADE</label>
                                                                <div></div>
                                                            </div>
                                                        </div>
                                                        <div class="back">
                                                            <div class="strip"></div>
                                                            <div class="logo" id="cartao_logo">
                                                                <!-- IMAGEM CARREGADA COM JAVASCRIPT -->
                                                            </div>
                                                            <div class="ccv">
                                                                <label>CVV</label>
                                                                <div></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 input-group hidden-m" style="margin-top:18px;">
                                                    <select id="select_parcelas_desktop">
                                                        <option value=""></option>
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                        <!-- FIM DA ROW DE FORM DO CARTÃO E CARTÃO INTERATIVO -->
                                    </div>

                                    <!-- ROW DE CUPOM E BOTÃO FINALIZAR -->
                                    <div class="finalizar row justify-content-between align-items-center centralizar">

                                        <!-- INSIRA CUPOM -->
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 text-center">
                                            <a id="bt_modal_cupom" href="#" data-toggle="modal" data-target="#modalCupom"> 
                                                <i data-feather="tag" class="align-middle" style="margin-right: 5px;"></i>
                                                Inserir cupom de desconto
                                            </a>
                                            <span id="desconto_cupom" style="display: none"> 
                                                <p id="msg_desconto"></p>
                                            </span>
                                        </div>

                                        <!-- BOTÃO FINALIZAR -->
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 text-center">
                                            <button id="finalizar_compra_cartao" class="btn-finalizar btn btn-primary" type="button" disabled> 
                                                <i data-feather="lock" class="align-middle" style="margin: 8px; height: 20px;"></i> 
                                                FINALIZAR COMPRA
                                            </button>
                                        </div>
                                    </div>

                                </div>
                                <!-- FIM DA TAB DE PAGAMENTO DE CARTÃO -->
                            </li>

                            <!-- PAGAMENTOS EM BOLETO -->
                            <li>
                                <div class="link" id="accordion_boleto">
                                    
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" class="align-middle icone-tab"><path d="M21 6c.551 0 1 .449 1 1v10c0 .551-.449 1-1 1h-18c-.551 0-1-.449-1-1v-10c0-.551.449-1 1-1h18zm0-2h-18c-1.657 0-3 1.343-3 3v10c0 1.657 1.343 3 3 3h18c1.657 0 3-1.343 3-3v-10c0-1.657-1.343-3-3-3zm-17 12v-8h2v8h-2zm12 0v-8h2v8h-2zm-9 0v-8h1v8h-1zm2 0v-8h2v8h-2zm3 0v-8h1v8h-1zm2 0v-8h1v8h-1zm5 0v-8h1v8h-1z"></path></svg>          
                                <strong> Boleto </strong> 
                                <i data-feather="chevron-right" class="chevron-down align-middle"></i>
                                    <span class="a_vista"><i class="fa fa-exclamation-circle"> </i> À VISTA </span>
                                </div>

                                <!-- AVISOS SOBRE BOLETO -->
                                <div class="tabpag" id="pagCartao">
                                    <div class="row d-flex justify-content-end">
                                        <div class="col-12">
                                            <p>Os pagamentos efetuados via Boleto Bancário não podem ser parcelados. Seu produto será reservado e enviado somente a confirmação do pagamento</p>

                                            <ul class="lista-boleto">
                                                <li>Você deve pagar seu boleto antes da data do vencimento;</li>
                                                <li>O pagamento leva em torno de 2 dias úteis para ser processado;</li>
                                            </ul>
                                        </div>

                                        <!-- BOTÃO GERAR BOLETO -->
                                        <div class="col-12">
                                            <div class="col-lg text-right centralizar">
                                                <button id="finalizar_compra_boleto" class="btn-finalizar btn btn-primary" type="button" disabled>
                                                    <i data-feather="lock" class="align-middle" style="margin: 8px; height: 20px;"></i> 
                                                    GERAR BOLETO
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>

        </div>
    </form>

    <footer>
        <img src="{{ asset('assets/img/logoCloudfox.png') }}">
    </footer>

    <!-- FIM CONTEÚDO  -->
    <script src="{{ asset('assets/js/jquery-3.3.1.min.js') }}"></script>

	{{--  <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
  --}}
    <script src="{{ asset('assets/js/cartao.js') }}"></script>

    {{--  <script src="{{ asset('assets/js/jquery.mask.js') }}"></script>  --}}

    {{--<script src="{{ asset('assets/js/logs.js') }}"></script> --}}

    <script src="https://assets.pagar.me/pagarme-js/3.0/pagarme.min.js"></script>

    {{--  <script src="{{ asset('assets/js/validacao-checkout.js') }}"></script>  --}}

    {{--  <script src="{{ asset('assets/js/servicos.js') }}"></script>  --}}

    {{--  <script src="{{ asset('assets/js/pagar-me.js') }}"></script>  --}}

    {{--  <script src="{{ asset('assets/js/jquery-creditcardvalidator/jquery.creditCardValidator.js') }}"></script>  --}}

  	{{--  <script src="{{ asset('assets/js/popper.min.js') }}"></script>  --}}

    <script src="{{ asset('assets/js/bootstrap-4.1.3.min.js') }}"></script>

    <script>

        var prevScrollpos = window.pageYOffset;

        window.onscroll = function() {

            var currentScrollPos = window.pageYOffset;

            if (prevScrollpos > currentScrollPos) {
                document.getElementById("topbar").style.top = "0";
            } 
            else {
                document.getElementById("topbar").style.top = "-100px";
            }

            prevScrollpos = currentScrollPos;
        }

    </script>

</body>

</html>

