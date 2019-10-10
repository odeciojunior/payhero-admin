@extends("layouts.master")

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <div class="row jusitfy-content-between" style="min-height:56px">
                <div class="col-lg-8 d-flex align-items-center">
                    <h1 class="page-title">Integrações com Shopify</h1>
                </div>
                <div class="col text-right" id="integration-actions" style="display:none">
                    <a data-toggle="modal" id='btn-integration-model' class="btn btn-floating btn-danger ml-10" style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                        <i class="icon wb-plus" aria-hidden="true"></i>
                    </a>
                    {{-- <a data-toggle="modal" data-target='#modal_explicacao' class="btn btn-floating" style="background-color:blue;position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                        <i class="icon wb-help" aria-hidden="true"></i>
                    </a> --}}
                </div>
            </div>
        </div>
        <div class="page-content container">
            <div class="row" id="content">
                {{-- js load dynamically --}}
            </div>

            {{-- Modal add-edit integration --}}
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_add_integracao" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
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
                            <button id="bt_integration" type="button" class="btn btn-success" data-dismiss="modal"></button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        {{-- End Modal  --}}

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
        <script src="/modules/shopify/js/index.js?v=2"></script>
    @endpush

@endsection

