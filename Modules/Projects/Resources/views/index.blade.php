@extends("layouts.master")
@section('content')
    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/global/css/switch.css') }}">
    @endpush
    <!-- Page -->
    <div class="page">
        <div style="display: none" class="page-header container">
            <a href="/projects/create" class="btn btn-floating btn-primary" id="btn-add-project"
               style="position: relative; float: right; display:none" title='Adicionar projeto'>
                <i class="icon wb-plus" aria-hidden="true" style="margin-top:8px"></i>
            </a>
            <a id="btn-config" class="mr-20 btn btn-floating bg-primary" style="position: relative; float: right;" title='Configurações'>
                <i class="icon wb-settings text-white" aria-hidden="true" style="margin-top:8px"></i>
            </a>
            <h1 class="page-title">Meus projetos</h1>
            <div class="page-header-actions">
            </div>
        </div>
        <div class="page-content container">
            <div id="data-table-projects" class="row" style="margin-top: 30px">
            </div>
        </div>
        @include('projects::empty')
    </div>
    <!-- Modal add integração -->
    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_config"
         aria-hidden="true"
         aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
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
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->
    @push('scripts')
        <script src="{{asset('modules/projects/js/index.js?v=' . random_int(100, 10000))}}"></script>
    @endpush

@endsection

