@extends("layouts.master")

@section('content')
    @push('css')
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/empty.css') }}">
        <link rel="stylesheet" href="{{ asset('/modules/global/css/switch.css') }}">
    @endpush
    <div class="page">
        <div class="page-header container">
            <button id="add-collaborator" title='Adicionar colaborador' type="button"
                    class="btn btn-floating btn-danger"
                    style="position: relative; float: right" {{--data-target='#modal' data-toggle='modal'--}}>
                <i class="icon wb-plus" aria-hidden="true"></i></button>
            <h2 class="page-title">Colaboradores</h2>
        </div>
        <div class="page-content container" id='page-invites'>
            <div id="content-error" class='' style='display:none;'>
                <div class="content-modal-error text-center" style=''>
                    <img src="modules/global/img/emptyconvites.svg" width="250px"/>
                    <h4 class="big gray" style='width:100%'>Você ainda não cadastrou colaboradores!</h4> <br>
                    {{--                    <p class="desc gray" style='width:100%'>Envie convites, e--}}
                    {{--                        <strong>ganhe 1% de tudo que seu convidado vender durante um ano!</strong></p>--}}
                </div>
            </div>
            <div class="card shadow mt-30" id='card-table-collaborators' data-plugin="matchHeight"
                 style='display:none;'>
                <div class="tab-pane active" id="tab_convites_enviados" role="tabpanel">
                    <table id='table-collaborators' class="table table-striped unify">
                        <thead class="text-center">
                        <th class="text-left">Colaborador</th>
                        <th class="text-center">Nome</th>
                        <th class="text-center">Email</th>
                        <th class="text-center">Função</th>
                        <th class="text-center">Data cadastro</th>
                        <td class='table-title text-center options-column-width'>Opções</td>
                        </thead>
                        <tbody id='table-body-collaborators'>
                        {{-- js invites carrega  --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <ul id="pagination-collaborators" class="pagination-sm margin-chat-pagination"
                style="margin-top:10px;position:relative;float:right">
                {{-- js pagination carrega --}}
            </ul>
            <div class="modal fade modal-3d-flip-vertical" id="modal-invite" aria-labelledby="exampleModalTitle"
                 role="dialog" tabindex="-1">
                <div id='mainModalBody' class="modal-dialog modal-simple">
                </div>
            </div>
        </div>
    </div>
    <!-- Modal add integração -->
    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_add_collaborator"
         aria-hidden="true"
         aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg d-flex justify-content-center">
            <div class="modal-content w-450" id="conteudo_modal_add">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" style="font-weight: 700;"></h4>
                </div>
                <div class="pt-10 pr-20 pl-20 modal_collaborator_body">
                    @include('collaborators::create')
                    @include('collaborators::edit')
                </div>
                <div class="modal-footer" style="margin-top: 15px">
                    <button id="btn_collaborator" type="button" class="btn btn-success"></button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!-- Modal padrão para excluir -->
    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete-invitation" aria-hidden="true"
         aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
        <div class="modal-dialog  modal-dialog-centered  modal-simple">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <a class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close"
                       id="fechar_modal_excluir">
                        <i class="material-icons md-16">close</i>
                    </a>
                </div>
                <div id="modal_excluir_body" class="modal-body text-center p-20">
                    <div class="d-flex justify-content-center">
                        <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                    </div>
                    <h3 class="black"> Você tem certeza? </h3>
                    <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center">
                    <button id='btn-cancel-invitation' type="button" class="col-4 btn btn-gray" data-dismiss="modal"
                            style="width: 20%;">Cancelar
                    </button>
                    <button id="btn-delete-collaborator" type="button" class="col-4 btn btn-danger" style="width: 20%;"
                            data-dismiss="modal">Excluir
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{asset('modules/collaborators/js/index.js?v=1') }}"></script>
    @endpush

@endsection

