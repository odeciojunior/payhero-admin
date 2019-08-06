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
                    <a data-toggle="modal" id='btn-integration-model' class="btn btn-floating btn-danger" style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                        <i class="icon wb-plus" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="page-content container">
            @if(count($projects) == 0)
                @push('css')
                    <link rel="stylesheet" href="{!! asset('modules/global/assets/css/empty.css') !!}">
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
        </div>
    </div>

    @push('scripts')
        <script src="/modules/shopify/js/index.js"></script>
    @endpush

@endsection

