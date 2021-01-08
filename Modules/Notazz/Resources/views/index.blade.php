@extends("layouts.master")
@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/notazz/css/index.css?v=01') }}">
    <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=02') !!}">
@endpush
@section('content')
    <div id='project-content'>
        <div class='page'>
            <div style="" class="page-header container">
                <div class="row jusitfy-content-between">
                    <div class="col-lg-8">
                        <h1 class="page-title">Integrações com Notazz</h1>
                    </div>
                    <div class="col text-right">
                        <a data-toggle="modal" id='btn-add-integration' class="btn btn-floating btn-primary" style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                            <i class="o-add-1" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class='page-content container' id='project-integrated'>
                <div class="row" id="content">
                    {{-- js load dynamically --}}
                </div>
                <div id="no-integration-found" class='row justify-content-center' style="display:none">
                    <div class="content-error text-center">
                        <img src="{!! asset('modules/global/img/empty.svg') !!}" width="250px">
                        <h1 class="big gray"><strong>Nenhuma integração encontrada!</strong></h1>
                        <p class="desc gray">Integre seus projetos com Notazz de forma totalmente automatizada!</p>
                    </div>
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
                                @include('notazz::create')
                                @include('notazz::edit')
                            </div>
                            <div class="modal-footer" style="margin-top: 15px">
                                <button id="bt_integration" type="button" class="btn btn-success" data-dismiss="modal"></button>
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- End Modal  --}}
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{asset('modules/notazz/js/index.js?v=s1')}}"></script>
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
    @endpush
@endsection
