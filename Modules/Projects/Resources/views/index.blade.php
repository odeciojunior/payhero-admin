@extends("layouts.master")
@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            @if($projects->count() > 0)
                <a href="/projects/create" class="btn btn-floating btn-danger" style="position: relative; float: right">
                    <i class="icon wb-plus" aria-hidden="true" style="margin-top:8px"></i>
                </a>
            @endif
            <h1 class="page-title">Meus projetos</h1>
            <div class="page-header-actions">
            </div>
        </div>
        <div class="page-content container">

            @if($projects->count() > 0)
                <div class="row" style="margin-top: 30px">
                    @foreach($projects as $project)
                        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                            <div class="card">
                                <img class="card-img-top" src="{!! $project->photo != '' ? $project->photo : '/modules/global/assets/img/projeto.png' !!}" alt="">
                                <div class="card-body">
                                    <h5 class="card-title">{{$project->name}}</h5>
                                    <p class="card-text sm">Criado em {!! $project->created_at->format('d/m/Y') !!}</p>
                                    <a href="/projects/{{Hashids::encode($project->id)}}" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_detalhes" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-simple">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h4 id="modal_detalhes_titulo" class="modal-title" style="width: 100%; text-align:center"></h4>
                            </div>
                            <div id="modal_detalhes_body" class="modal-body">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_excluir" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-simple">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h4 id="modal_excluir_titulo" class="modal-title" style="width: 100%; text-align:center"></h4>
                            </div>
                            <div id="modal_excluir_body" class="modal-body">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                <a id="excluir_projeto" class="btn btn-success">Confirmar</a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                @push('css')
                    <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
                @endpush

                <div class="content-error d-flex text-center">        
                    <img src="{!! asset('modules/global/assets/img/emptyprojetos.svg') !!}" width="250px">
                    <h1 class="big gray">Você ainda não tem nenhum projeto!</h1>
                    <p class="desc gray">Que tal criar um primeiro projeto para começar a vender? </p>
                    <a href="/projects/create" class="btn btn-primary gradient">Cadastrar primeiro projeto</a>
                </div>

            @endif
        </div>
    </div>

    @push('scripts')

    @endpush

@endsection

