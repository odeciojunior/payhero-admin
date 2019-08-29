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
                        <h1 class="page-title">Integrações com a Notazz</h1>
                    </div>
                    <div class="col text-right">
                        <a data-toggle="modal" id='btn-add-integration' class="btn btn-floating btn-danger" style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                            <i class="icon wb-plus" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class='page-content container' id='project-integrated'>
{{--                integrations here--}}
            <!-- Modal add integração -->
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
                            </div>
                            <div class="modal-footer" style="margin-top: 15px">
                                <button id="bt_integration" type="button" class="btn btn-success" data-dismiss="modal"></button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Modal -->
                <div id="modal-project" class="modal fade modal-3d-flip-vertical " role="dialog" tabindex="-1">
                    <div id="modal_add_size" class="modal-dialog modal-dialog-centered modal-simple ">
                        <div id="conteudo_modal_add" class="modal-content p-10">
                            <div class="header-modal simple-border-bottom">
                                <h2 id="modal-project-title" class="modal-title"></h2>
                            </div>
                            <div id="modal_project_body" class="modal-body simple-border-bottom" style='padding-bottom:1%;padding-top:1%;'>
                            </div>
                            <div id='modal-withdraw-footer' class="modal-footer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script src="/modules/notazz/js/index.js"></script>
    @endpush
@endsection
