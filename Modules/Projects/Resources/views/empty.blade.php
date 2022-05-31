{{--<div id="project-empty" class="content-error text-center d-none">--}}
<div id="project-empty" class="content-error text-center" style="display:none !important;
                                                                height: 100%;
                                                                width: 100%;
                                                                position: relative;
                                                                display: -webkit-flex;
                                                                display: flex;
                                                                -webkit-align-items: center;
                                                                align-items: center;
                                                                -webkit-justify-content: center;
                                                                justify-content: center;
                                                                padding-bottom:116px">
    <img src="{!! mix('build/global/img/projetos.svg') !!}" width="156px">
    <h1 class="big gray">Você ainda não tem nenhuma loja!</h1>
    <p class="desc gray">Que tal criar uma primeira loja para começar a vender? </p>
    <a href="/projects/" class="btn btn-primary btn-floating text-center align-items-center d-flex justify-content-center text-white" style="position: relative;">
        <i class="o-add-1" aria-hidden="true"></i>
    </a>
</div>
@push('css')
    <link rel="stylesheet" href="{!! mix('build/layouts/projects/empty.min.css') !!}">
@endpush
