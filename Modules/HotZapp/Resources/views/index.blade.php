@extends("layouts.master")
@push('css')
    <style type='text/css'>
        /* SWITCH CONFIG */
        label.switch {
            margin-bottom: 0 !important;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 35px;
            height: 15px;
            margin-right: 15px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: -3px;
            top: -2px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
            box-shadow: 0 10px 10px 0 rgba(0, 0, 0, 0.15);
        }
        input:checked + .slider {
            background-color: #f78d1e;
        }
        input:focus + .slider {
            box-shadow: 0 0 1px #f78d1e;
        }
        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }
        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }
        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@endpush
@section('content')
    <div id='project-content'>
        <div class='page'>
            <div class="page-header container">
                <div class="row jusitfy-content-between">
                    <div class="col-lg-8">
                        <h1 class="page-title">Integrações com HotZapp</h1>
                    </div>
                    <div class="col text-right">
                        <a data-toggle="modal" data-target="#modal_add_integracao" class="btn btn-floating btn-danger" style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                            <i class="icon wb-plus" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class='page-content container'>
                @if(count($projectsIntegrated) == 0)
                    <div class="row justify-content-center mt-30">
                        <h4>Nenhuma integração encontrada</h4>
                    </div>
                @else

                    <div class="clearfix"></div>

                    <div class="row">
                        @foreach($projectsIntegrated as $project)
                            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                                <div class="card shadow card-edit" project='{{\Hashids::encode($project->id)}}' style='cursor:pointer;'>
                                    <img class="card-img-top img-fluid w-full" src="{!! $project['photo'] !!}" onerror="this.onerror=null;this.src='{!! asset('modules/global/assets/img/produto.png') !!}';" alt="{!! asset('modules/global/assets/img/produto.png') !!}">
                                    <div class="card-body">
                                        <h4 class="card-title"> {!! $project['name'] !!}</h4>
                                        <p class="card-text sm">Criado em {!! $project->created_at->format('d/m/Y') !!}</p>
                                    </div>
                                </div>
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
                                <h4 class="modal-title" style="font-weight: 700;">Adicionar nova Integração com HotZapp</h4>
                            </div>
                            <div class="pt-10 pr-20 pl-20 modal_integracao_body">
                                <form id='form_add_integration' method="post" action="#">
                                    @csrf
                                    <div style="width:100%">
                                        <div class="row mt-20">
                                            <div class="col-12">
                                                <div class='form-group'>
                                                    <label for="company">Selecione seu projeto</label>
                                                    <select class="select-pad" id="project_id" name="project_id">
                                                        @foreach($projects as $project)
                                                            <option value="{!! $project['id'] !!}">{!! $project['name'] !!}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class='form-group col-12'>
                                                <label for="url_store">Link</label>
                                                <div class="d-flex input-group">
                                                    <input type="text" class="input-pad addon" name="link" id="link" placeholder="Digite o link">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-20">
                                            <div class="col-6">
                                                <div class="switch-holder">
                                                    <label for="token" class='mb-10'>Boleto gerado:</label>
                                                    <br>
                                                    <label class="switch">
                                                        <input type="checkbox" value='0' name="boleto_generated" id="boleto_generated" class='check'>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="switch-holder">
                                                    <label for="token" class='mb-10'>Boleto pago:</label>
                                                    <br>
                                                    <label class="switch">
                                                        <input type="checkbox" value='0' name="boleto_paid" id="boleto_paid" class='check'>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-20">
                                            <div class="col-6">
                                                <div class="switch-holder">
                                                    <label for="token" class='mb-10'>Cartão de crédito pago:</label>
                                                    <br>
                                                    <label class="switch">
                                                        <input type="checkbox" value='0' name="credit_card_paid" id="credit_card_paid" class='check' value='0'>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="switch-holder">
                                                    <label for="token" class='mb-10'>Cartão de crédito Recusado:</label>
                                                    <br>
                                                    <label class="switch">
                                                        <input type="checkbox" value='0' name="credit_card_refused" id="credit_card_refused" class='check' value='0'>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer" style="margin-top: 15px">
                                <button id="bt_add_integration" type="button" class="btn btn-success" data-dismiss="modal">Adicionar integração</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Modal -->
            </div>
        </div>
    </div>


    @push('scripts')
        <script src="/modules/hotzapp/js/index.js"></script>
    @endpush
@endsection
