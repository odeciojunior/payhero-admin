@if(count($projectsIntegrated) == 0)
    @push('css')
        <link rel="stylesheet" href="{!! asset('modules/global/assets/css/empty.css') !!}">
    @endpush
    <div class="content-error d-flex text-center">
        <img src="{!! asset('modules/global/assets/img/emptyconvites.svg') !!}" width="250px">
        <h1 class="big gray">Nenhuma integração encontrada!</h1>
        <p class="desc gray">Integre seus projetos com HotZapp de forma totalmente automatizada!</p>
    </div>
@else

    <div class="clearfix"></div>

    <div class="row">
        @foreach($projectsIntegrated as $project)
            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                <div class="card shadow card-edit" project='{{\Hashids::encode($project->id)}}' style='cursor:pointer;'>
                    <img class="card-img-top img-fluid w-full" src="{!! $project['photo'] !!}" onerror="this.onerror=null;this.src='{!! asset('modules/global/assets/img/produto.png') !!}';" alt="{!! asset('modules/global/assets/img/produto.png') !!}">
                    <div class="card-body">
                        <div class='row'>
                            <div class='col-md-10'>
                                <h4 class="card-title"> {!! $project['name'] !!}</h4>
                                <p class="card-text sm">Criado em {!! $project->created_at->format('d/m/Y') !!}</p>
                            </div>
                            <div class='col-md-2'>
                                <a role='button' class='delete-integration pointer float-right mt-35' project='{{\Hashids::encode($project->id)}}' data-toggle='modal' data-target='#modal-delete' type='a'>
                                    <i class='material-icons gradient'>delete_outline</i></a>
                            </div>
                        </div>
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
                <h4 class="modal-title" style="font-weight: 700;"></h4>
            </div>
            <div class="pt-10 pr-20 pl-20 modal_integracao_body">
            </div>
            <div class="modal-footer">
                <button id="bt_integration" type="button" class="btn btn-success mt-40" data-dismiss="modal"></button>
                <button type="button" class="btn btn-danger mt-40" data-dismiss="modal">Fechar</button>
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
