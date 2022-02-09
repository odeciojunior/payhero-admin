@extends("layouts.master")
@section('content')
    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/global/css/switch.css?v=124') }}">
        <link rel="stylesheet" href="{{ asset('/modules/projects/css/index.css') }}">
        <style>
            .card{
                min-height:403px;
            }
            .card-title{
                min-height:56px;
            }
        </style>
    @endpush
    <!-- Page -->
    <div class="page">
        <div style="display: none" class="page-header container">
            <div class="row align-items-center justify-content-between" style="min-height:50px">
                <div class="col-8">
                    <h1 class="page-title">Meus projetos</h1>
                    <div class="page-header-actions"></div>
                </div>
                <div class="col-4 text-right">
                    <a href="/projects/create" class="btn btn-floating btn-primary" id="btn-add-project" style="position: relative; float: right; display:none" title='Adicionar projeto'>
                        <span style="color: white; font-size: 35px" class='o-add-1'></span>
                    </a>
                    <a id="btn-config" class="mr-20 btn-config btn btn-floating bg-secondary d-flex justify-content-center align-items-center" style="position: relative; float: right;" title='Configurações'>
                        <span class="o-cogwheel-1 text-white" style="font-size: 26px; font-weight: 900 !important;"></span>
                    </a>
                </div>
            </div>
        </div>

        @include('projects::empty')
        @include('projects::empty-company')
        <div class="page-content container" style="padding-top: 0">
            <div id="data-table-projects" class="row" style="margin-top: 0">
            </div>
        </div>

    </div>
    <!-- Modal add integração -->
    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_config" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg d-flex justify-content-center modal-dialog-centered">

            <div class="modal-content w-450" id="conteudo_modal_add">

                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>

                    <h4 class="modal-title" style="font-weight: 700;">
                        Configurações
                    </h4>

                </div>

                <div class="pt-10 pr-20 pl-20 modal_config_body">

                    <div class="switch-holder">
                        <label for='deleted_project_filter' class='mb-10'>Apresentar projetos excluídos nos filtros:</label>
                        <br>

                        <label class="switch">
                            <input type="checkbox" class='check' value='{{auth()->user()->deleted_project_filter}}' name="deleted_project_filter" id="deleted_project_filter">
                            <span class="slider round"></span>
                        </label>

                    </div>
                </div>

                <div class="modal-footer" style="margin-top: 15px">
                    <button id="btn_save_config" type="button" class="btn btn-success">Salvar</button>
                </div>

            </div>

        </div>

    </div>
    <!-- End Modal -->
    @push('scripts')
        <script src="{{asset('modules/projects/js/index.js?v='.uniqid())}}"></script>
    @endpush
@endsection

