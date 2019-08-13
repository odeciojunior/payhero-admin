@extends("layouts.master")

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <div class="row jusitfy-content-between">
                <div class="col-lg-8">
                    <h1 class="page-title">Integrações com Shopify</h1>
                </div>
                <div class="col text-right">
                    <a data-toggle="modal" id='btn-integration-model' class="btn btn-floating btn-danger ml-10" style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                        <i class="icon wb-plus" aria-hidden="true"></i>
                    </a>

                    <a data-toggle="modal" data-target='#modal_explicacao' class="btn btn-floating" style="background-color:blue;position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                        <i class="icon wb-help" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="page-content container">
            @if(count($projects) == 0)
                @push('css')
                    <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
                @endpush
                <div class="content-error d-flex text-center">
                    <img src="{!! asset('modules/global/assets/img/emptyconvites.svg') !!}" width="250px">
                    <h1 class="big gray">Nenhuma integração encontrada!</h1>
                    <p class="desc gray">Integre suas lojas Shopify com o checkout do Cloudfox!</p>
                </div>
            @else

                <div class="clearfix"></div>

                <div class="row">
                    @foreach($projects as $project)
                        <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                            <a href="/projects/{!! Hashids::encode($project['id']) !!}" class="streched-link">
                                <div class="card shadow">
                                    <img class="card-img-top img-fluid w-full" src="{!! $project['photo'] !!}" onerror="this.onerror=null;this.src='{!! asset('modules/global/assets/img/produto.png') !!}';" alt="{!! asset('modules/global/assets/img/produto.png') !!}">
                                    <div class="card-body">
                                        <h4 class="card-title"> {!! $project['name'] !!}</h4>
                                        <p class="card-text sm">Criado em {!! $project->created_at->format('d/m/Y') !!}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

        <!-- Modal add integração -->
            @if(count($companies) > 0)
                <div class="modal fade example-modal-lg modal-3d-flip-vertical modal_integration_shopify" id="modal_add_integracao" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-lg d-flex justify-content-center">
                        <div class="modal-content w-450" id="conteudo_modal_add">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title" style="font-weight: 700;">Adicionar nova integração com Shopify</h4>
                            </div>
                            <div class="pt-10 pr-20 pl-20">
                                <form id='form_add_integration' method="post" action="#">
                                    @csrf
                                    <div style="width:100%">
                                        <div class="row">
                                            <div class="col-12">
                                                <label for="token">Token (password)</label>
                                                <input type="text" class="input-pad" name="token" id="token" placeholder="Password da chave de integração">
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top:30px">
                                            <div class="input-group col-12">
                                                <label for="url_store">URL da sua loja no Shopify</label>
                                                <div class="d-flex input-group">
                                                    <input type="text" class="input-pad col-7 addon" name="url_store" id="url_store" placeholder="Digite a URL da sua loja">
                                                    <span class="input-group-addon input-pad col-lg-5">.myshopify.com</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top:30px">
                                            <div class="col-12">
                                                <label for="company">Selecione sua empresa</label>
                                                <select class="select-pad" id="company" name="company">
                                                    @foreach($companies as $company)
                                                        <option value="{!! $company['id'] !!}">{!! $company['fantasy_name'] !!}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer" style="margin-top: 15px">
                                <button id="bt_add_integration" type="button" class="btn btn-success" data-dismiss="modal">Realizar integração</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div id="modal-company" class="modal fade modal-3d-flip-vertical modal_integration_shopify" role="dialog" tabindex="-1">
                    <div id="modal_add_size" class="modal-dialog modal-dialog-centered modal-simple ">
                        <div id="conteudo_modal_add" class="modal-content p-10">
                            <div class="header-modal simple-border-bottom">
                                <h2 id="modal-project-title" class="modal-title">Oooppsssss!</h2>
                            </div>
                            <div id="modal_project_body" class="modal-body simple-border-bottom" style='padding-bottom:1%;padding-top:1%;'>
                                <div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;">
                                    <span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span>
                                </div>
                                <h3 align="center"><strong>Você não possui empresa para realizar integração</strong>
                                </h3>
                                <h5 align="center">Deseja criar sua primeira empresa?
                                    <a class="red pointer" href="{{route('companies.create')}}">clique aqui</a>
                                </h5>
                            </div>
                            <div id='modal-withdraw-footer' class="modal-footer">
                                <div style="width:100%;text-align:center;padding-top:3%">
                                    <span class="btn btn-success" data-dismiss="modal" style="font-size: 25px">Retornar</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
        <!-- End Modal -->
                <!-- End Modal -->
                <!-- Modal Explicação -->
                <div class="modal fade modal-3d-flip-vertical" id="modal_explicacao" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content" id="conteudo_modal_explicacao">
                            <div class="panel-group panel-group-continuous m-0" id="acordionHelp" aria-multiselectable="true" role="tablist">
                                <div class="panel">
                                    <div class="panel-heading" id="exampleHeadingFirst" role="tab">
                                        <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse" href="#exampleCollapseFirst" aria-controls="exampleCollapseFirst" aria-expanded="false">
                                            <strong>Primeiro passo</strong>
                                        </a>
                                    </div>
                                    <div class="panel-collapse collapse" id="exampleCollapseFirst" aria-labelledby="exampleHeadingFirst" role="tabpanel" style="" data-parent="#acordionHelp">
                                        <div class="panel-body justify-content-center">
                                            <div class="d-flex align-items-center">
                                            <span> Crie uma loja no shopify (<a onclick='openInNewWindow("https://www.shopify.com/")' href='#'>https://www.shopify.com/</a>)<br>
                                             Caso já tenha sua loja, apenas efetue o <strong>log in</strong>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel">
                                    <div class="panel-heading" id="exampleHeadingSecond" role="tab">
                                        <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse" href="#exampleCollapseSecond" aria-controls="exampleCollapseSecond" aria-expanded="false">
                                            <strong>Segundo passo</strong>
                                        </a>
                                    </div>
                                    <div class="panel-collapse collapse" id="exampleCollapseSecond" aria-labelledby="exampleHeadingSecond" role="tabpanel" style="" data-parent="#acordionHelp">
                                        <div class="panel-body justify-content-center">
                                            <div class="d-flex align-items-center">
                                            <span>Apos ter se autenticado no shopify, clique em "Apps" <strong class='grad'>(como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail thumbnail' src='https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/shoify-integration-step-1.png' style='width:100%'>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel">
                                    <div class="panel-heading" id="exampleHeadingThird" role="tab">
                                        <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse" href="#exampleCollapseThird" aria-controls="exampleCollapseThird" aria-expanded="false">
                                            <strong>Terceiro passo</strong>
                                        </a>
                                    </div>
                                    <div class="panel-collapse collapse" id="exampleCollapseThird" aria-labelledby="exampleHeadingThird" role="tabpanel" style="" data-parent="#acordionHelp">
                                        <div class="panel-body justify-content-center">
                                            <div class="d-flex align-items-center">
                                            <span>Ao carregar a página, identifique e clique no link "Manage private apps" <strong class='grad'>(como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail thumbnail' src='https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/shoify-integration-step-2.png' style='width:100%'>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel">
                                    <div class="panel-heading" id="exampleHeadingFourth" role="tab">
                                        <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse" href="#exampleCollapseFourth" aria-controls="exampleCollapseFourth" aria-expanded="false">
                                            <strong>Quarto passo</strong>
                                        </a>
                                    </div>
                                    <div class="panel-collapse collapse" id="exampleCollapseFourth" aria-labelledby="exampleHeadingFourth" role="tabpanel" style="" data-parent="#acordionHelp">
                                        <div class="panel-body justify-content-center">
                                            <div class="d-flex align-items-center">
                                            <span>Aguarde a nova pagina abrir, e clique no botão "Create a new private app" <strong class='grad'>(como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail thumbnail' src='https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/shoify-integration-step-3.png' style='width:100%'>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel">
                                    <div class="panel-heading" id="exampleHeadingFifth" role="tab">
                                        <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse" href="#exampleCollapseFifth" aria-controls="exampleCollapseFifth" aria-expanded="false">
                                            <strong>Quinto passo</strong>
                                        </a>
                                    </div>
                                    <div class="panel-collapse collapse" id="exampleCollapseFifth" aria-labelledby="exampleHeadingFifth" role="tabpanel" style="" data-parent="#acordionHelp">
                                        <div class="panel-body justify-content-center">
                                            <div class="d-flex align-items-center">
                                            <span>Na nova pagina você deverá preencher alguns dados.
                                                <br> <strong>"Private app name"</strong> é o campo onde ficara o nome do novo aplicativo, para não confundir, sugerimos que ultilize "cloudfox".
                                                <br> <strong>"Emergency developer email"</strong> é o campo do seu email, preencha-o corretamente.
                                                <img class='img-thumbnail thumbnail' src='https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/shoify-integration-step-4-1.png' style='width:100%'>
                                       </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel">
                                    <div class="panel-heading" id="exampleHeadingSixth" role="tab">
                                        <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse" href="#exampleCollapseSixth" aria-controls="exampleCollapseSixth" aria-expanded="false">
                                            <strong>Sexto passo</strong>
                                        </a>
                                    </div>
                                    <div class="panel-collapse collapse" id="exampleCollapseSixth" aria-labelledby="exampleHeadingSixth" role="tabpanel" style="" data-parent="#acordionHelp">
                                        <div class="panel-body justify-content-center">
                                            <div class="d-flex align-items-center">
                                            <span>Depois de inserir corretamente os dados acima, precisamos que você nos libere algumas permissões.
                                                <br><strong>Fique bem atento, pois, as permissões listadas a seguir podem não estar em ordem, e se não forem liberadas, a integração não funcionara corretamente.</strong>
                                                <ul>
                                                    <li>Orders, transactions and fulfillments -> Read and write access</li>
                                                    <li>Products, variants and collections -> Read access</li>
                                                    <li>Theme templates and theme assets -> Read and write access</li>
                                                    <li>Product information -> Read access</li>
                                                    <li>Order editing -> Read and write access</li>
                                                    <li>Inventory -> Read access</li>
                                                </ul>
                                                <img class='img-thumbnail thumbnail' src='https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/shoify-integration-step-4-2.png' style='width:100%'>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel">
                                    <div class="panel-heading" id="exampleHeadingSeventh" role="tab">
                                        <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse" href="#exampleCollapseSeventh" aria-controls="exampleCollapseSeventh" aria-expanded="false">
                                            <strong>Setimo passo</strong>
                                        </a>
                                    </div>
                                    <div class="panel-collapse collapse" id="exampleCollapseSeventh" aria-labelledby="exampleHeadingSeventh" role="tabpanel" style="" data-parent="#acordionHelp">
                                        <div class="panel-body justify-content-center">
                                            <div class="d-flex align-items-center">
                                            <span>Confira os dados e clique em "save", o botão podera ser encontrado no final da pagina.
                                            <br> Uma janela de confirmação devera aparecer para você<strong class='grad'>(selecione o botão como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail' src='https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/shoify-integration-step-6.png' style='width:100%'>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel">
                                    <div class="panel-heading" id="exampleHeadingEigth" role="tab">
                                        <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse" href="#exampleCollapseEigth" aria-controls="exampleCollapseEigth" aria-expanded="false">
                                            <strong>Oitavo passo</strong>
                                        </a>
                                    </div>
                                    <div class="panel-collapse collapse" id="exampleCollapseEigth" aria-labelledby="exampleHeadingEigth" role="tabpanel" style="" data-parent="#acordionHelp">
                                        <div class="panel-body justify-content-center">
                                            <div class="d-flex align-items-center">
                                                <span>Agora você tem um novo app criado, para vincular com a nossa plataforma, clique no icone</span>
                                                <a class="btn btn-floating btn-danger" style="margin:15px;color: white;display: flex;align-items: center;justify-content: center;">
                                                    <i class="icon wb-plus" aria-hidden="true"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel">
                                    <div class="panel-heading" id="exampleHeadingNineth" role="tab">
                                        <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse" href="#exampleCollapseNineth" aria-controls="exampleCollapseNineth" aria-expanded="false">
                                            <strong>Nono passo</strong>
                                        </a>
                                    </div>
                                    <div class="panel-collapse collapse" id="exampleCollapseNineth" aria-labelledby="exampleHeadingNineth" role="tabpanel" style="" data-parent="#acordionHelp">
                                        <div class="panel-body justify-content-center">
                                            <div class="d-flex align-items-center">
                                            <span>O campo "Token (password)" deve ser preenchido com o password do seu app<strong class='grad'>(como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail thumbnail' src='https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/shoify-integration-step-7-1.png' style='width:100%'>
                                                <br>O campo "URL da sua loja no Shopify" sera a URL da sua loja. (sem o "https://" nem mesmo oque vier apos "myshopify.com")<strong class='grad'>(como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail thumbnail' src='https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/shopify-url.png' style='width:100%'>
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
    </div>

    @push('scripts')
        <script src="/modules/shopify/js/index.js"></script>
    @endpush

@endsection

