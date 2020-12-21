@extends("layouts.master")

@section('content')

    <!-- Page -->
    <div class="page">
        <div style="display: none" class="page-header container">
            <div class="row jusitfy-content-between" style="min-height:56px">
                <div class="col-lg-8 d-flex align-items-center">
                    <h1 class="page-title">Integrações com Shopify</h1>
                </div>
                <div class="col text-right" id="integration-actions" style="display:none">
                    <a data-toggle="modal" id='btn-integration-model' class="btn btn-floating btn-danger ml-10"
                       style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                        <i class="icon wb-plus" aria-hidden="true"></i>
                    </a>
                    <a data-toggle="modal" data-target='#modal_explicacao' class="btn btn-floating"
                       style="background-color:blue;position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                        <i class="icon wb-help" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="page-content container">
            <div class="row" id="content">
                {{-- js load dynamically --}}
            </div>

            {{-- Modal add-edit integration --}}
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_add_integracao" aria-hidden="true"
                 aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-lg d-flex justify-content-center">
                    <div class="modal-content w-450" id="conteudo_modal_add">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" style="font-weight: 700;"></h4>
                        </div>
                        <div class="pt-10 pr-20 pl-20 modal_integracao_body">
                            @include('shopify::create')
                        </div>
                        <div class="modal-footer" style="margin-top: 15px">
                            <button id="bt_integration" type="button" class="btn btn-success"
                                    data-dismiss="modal"></button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        {{-- End Modal  --}}

        <!-- Modal Explicação -->
            <div class="modal fade modal-3d-flip-vertical" id="modal_explicacao" aria-hidden="true"
                 aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content" id="conteudo_modal_explicacao">
                        <div class="panel-group panel-group-continuous m-0" id="acordionHelp"
                             aria-multiselectable="true" role="tablist">
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingFirst" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                       href="#exampleCollapseFirst" aria-controls="exampleCollapseFirst"
                                       aria-expanded="false">
                                        <strong>Primeiro passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseFirst"
                                     aria-labelledby="exampleHeadingFirst" role="tabpanel" style=""
                                     data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span> Crie uma loja no shopify: <a
                                                        onclick='openInNewWindow("https://www.shopify.com/")' href='#'>https://www.shopify.com/</a><br>
                                            Caso já tenha sua loja, apenas efetue o <strong>login</strong>.
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingSecond" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                       href="#exampleCollapseSecond" aria-controls="exampleCollapseSecond"
                                       aria-expanded="false">
                                        <strong>Segundo passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseSecond"
                                     aria-labelledby="exampleHeadingSecond" role="tabpanel" style=""
                                     data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span>Após ter se autenticado no shopify, clique em "Apps" <strong
                                                        class='grad'>(como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail thumbnail'
                                                     src='https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/shoify-integration-step-1.png'
                                                     style='width:100%'>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingThird" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                       href="#exampleCollapseThird" aria-controls="exampleCollapseThird"
                                       aria-expanded="false">
                                        <strong>Terceiro passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseThird"
                                     aria-labelledby="exampleHeadingThird" role="tabpanel" style=""
                                     data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span>Ao carregar a página, identifique e clique no link "Manage private apps" <strong
                                                        class='grad'>(como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail thumbnail'
                                                     src='https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/shoify-integration-step-2.png'
                                                     style='width:100%'>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingFourth" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                       href="#exampleCollapseFourth" aria-controls="exampleCollapseFourth"
                                       aria-expanded="false">
                                        <strong>Quarto passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseFourth"
                                     aria-labelledby="exampleHeadingFourth" role="tabpanel" style=""
                                     data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span>Aguarde a nova página abrir, e clique no botão "Create a new private app" <strong
                                                        class='grad'>(como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail thumbnail'
                                                     src='https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/shoify-integration-step-3.png'
                                                     style='width:100%'>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingFifth" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                       href="#exampleCollapseFifth" aria-controls="exampleCollapseFifth"
                                       aria-expanded="false">
                                        <strong>Quinto passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseFifth"
                                     aria-labelledby="exampleHeadingFifth" role="tabpanel" style=""
                                     data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span>Na nova página você deverá preencher alguns dados.
                                                <br> <strong>"Private app name"</strong> é o nome do novo aplicativo, para não confundir, sugerimos que ultilize "cloudfox".
                                                <br> <strong>"Emergency developer email"</strong> é o email para emergências, preencha-o corretamente.
                                                <img class='img-thumbnail thumbnail'
                                                     src='https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/shoify-integration-step-4-1.png'
                                                     style='width:100%'>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingSixth" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                       href="#exampleCollapseSixth" aria-controls="exampleCollapseSixth"
                                       aria-expanded="false">
                                        <strong>Sexto passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseSixth"
                                     aria-labelledby="exampleHeadingSixth" role="tabpanel" style=""
                                     data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span>Depois de inserir corretamente os dados acima, precisamos que você nos libere algumas permissões.
                                                <br><strong>Fique bem atento, pois, as permissões listadas a seguir podem não estar em ordem, e se não forem liberadas, a integração não funcionará corretamente.</strong>
                                                <ul>
                                                    <li>Orders, transactions and fulfillments -> Read and write access</li>
                                                    <li>Products, variants and collections -> Read access</li>
                                                    <li>Theme templates and theme assets -> Read and write access</li>
                                                    <li>Product information -> Read access</li>
                                                    <li>Order editing -> Read and write access</li>
                                                    <li>Inventory -> Read access</li>
                                                </ul>
                                                <img class='img-thumbnail thumbnail'
                                                     src='https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/shoify-integration-step-4-2.png'
                                                     style='width:100%'>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingSeventh" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                       href="#exampleCollapseSeventh" aria-controls="exampleCollapseSeventh"
                                       aria-expanded="false">
                                        <strong>Sétimo passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseSeventh"
                                     aria-labelledby="exampleHeadingSeventh" role="tabpanel" style=""
                                     data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span>Confira os dados e clique em "save", o botão podera ser encontrado no final da página.
                                            <br> Uma janela de confirmação aparecerá para você<strong class='grad'>(selecione o botão como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail'
                                                     src='https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/shoify-integration-step-6.png'
                                                     style='width:100%'>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingEigth" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                       href="#exampleCollapseEigth" aria-controls="exampleCollapseEigth"
                                       aria-expanded="false">
                                        <strong>Oitavo passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseEigth"
                                     aria-labelledby="exampleHeadingEigth" role="tabpanel" style=""
                                     data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span>Agora você tem um novo app criado, para vincular com a nossa plataforma, clique no botão</span>
                                            <a class="btn btn-floating btn-danger"
                                               style="margin:15px;color: white;display: flex;align-items: center;justify-content: center;">
                                                <i class="icon wb-plus" aria-hidden="true"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingNineth" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                       href="#exampleCollapseNineth" aria-controls="exampleCollapseNineth"
                                       aria-expanded="false">
                                        <strong>Nono passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseNineth"
                                     aria-labelledby="exampleHeadingNineth" role="tabpanel" style=""
                                     data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span>O campo "Token (password)" deve ser preenchido com o password do seu app<strong
                                                        class='grad'>(como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail thumbnail'
                                                     src='https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/shoify-integration-step-7-1.png'
                                                     style='width:100%'>
                                                <br>O campo "URL da sua loja no Shopify" sera a URL da sua loja. (sem o "https://" nem mesmo o que vier após "myshopify.com")<strong
                                                        class='grad'>(como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail thumbnail'
                                                     src='https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/shopify-url.png'
                                                     style='width:100%'>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal -->

        </div>
        @include('companies::empty')
        <div id="no-integration-found" class='row justify-content-center' style="display:none">
            <div class="content-error text-center">
                <img src="{!! asset('modules/global/img/emptyconvites.svg') !!}" width="250px">
                <h1 class="big gray"><strong>Nenhuma integração encontrada!</strong></h1>
                <p class="desc gray">Integre seus projetos com Shopify de forma totalmente automatizada!</p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('modules/shopify/js/index.js?v='. uniqid()) }}"></script>
    @endpush

@endsection

