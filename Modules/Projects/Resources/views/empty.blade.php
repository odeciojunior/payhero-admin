<div id="project-empty" class="content-error text-center" style="display:none;
                                                                height: 100%; 
                                                                width: 100%; 
                                                                position: absolute;
                                                                display: -webkit-flex;
                                                                display: flex;
                                                                -webkit-align-items: center;
                                                                align-items: center;
                                                                -webkit-justify-content: center;
                                                                justify-content: center;
                                                                padding-bottom:116px">
    <img src="{!! asset('modules/global/img/projetos.svg') !!}" width="156px">
    <h1 class="big gray">Você ainda não tem nenhum projeto!</h1>
    <p class="desc gray">Que tal criar um primeiro projeto para começar a vender? </p>
    <a href="/projects/create" class="btn btn-primary btn-floating text-center align-items-center d-flex justify-content-center text-white" style="position: relative;">
        <i class="o-add-1" aria-hidden="true"></i>
    </a>
</div>
@push('css')
    <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=03') !!}">
@endpush
