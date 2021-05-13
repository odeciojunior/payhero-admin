<div id="project-empty" class="content-error text-center" style="display:none">
    <img src="{!! asset('modules/global/img/projetos.svg') !!}" width="156px">
    <h1 class="big gray">Você ainda não tem nenhum projeto!</h1>
    <p class="desc gray">Que tal criar um primeiro projeto para começar a vender? </p>
    <a href="/projects/create" class="btn btn-primary">Cadastrar primeiro projeto</a>
</div>
@push('css')
    <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=02') !!}">
@endpush
