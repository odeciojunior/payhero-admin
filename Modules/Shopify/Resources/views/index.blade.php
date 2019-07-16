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
                    <a data-toggle="modal" data-target="#modal_add_integracao" class="btn btn-floating btn-danger" style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                        <i class="icon wb-plus" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="page-content container">
            @if(count($projects) == 0)
                <div class="row justify-content-center mt-30">
                    <h4>Nenhuma integração encontrada</h4>
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
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_add_integracao" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-lg d-flex justify-content-center">
                    <div class="modal-content w-450" id="conteudo_modal_add">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" style="font-weight: 700;">Adicionar nova integração com Shopify</h4>
                        </div>
                        <div class="pt-10 pr-20 pl-20">
                            <form id='form_add_integracao' method="post" action="#">
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
                        <div class="modal-footer" style="margin-top: 30px">
                            <button id="bt_adicionar_integracao" type="button" class="btn btn-success" data-dismiss="modal">Salvar</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
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

