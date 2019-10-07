<div id="project-empty" class="content-error text-center" style="display:none">
    <img src="{!! asset('modules/global/img/emptyprojetos.svg') !!}" width="250px">
    <h1 class="big gray">Você ainda não tem nenhum projeto!</h1>
    <p class="desc gray">Que tal criar um primeiro projeto para começar a vender? </p>
    <a href="/projects/create" class="btn btn-primary gradient">Cadastrar primeiro projeto</a>
</div>
@push('css')
    <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
@endpush
