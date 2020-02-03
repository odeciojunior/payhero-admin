@push('css')
    <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
@endpush
<div class="content-error text-center" id="empty-companies-error" style="display:none">
    <img src="{!! asset('modules/global/img/emptyempresas.svg') !!}" width="250px">
    <h1 class="big gray">Você ainda não tem nenhuma empresa!</h1>
    <p class="desc gray">Vamos cadastrar a primeira empresa? </p>
    <a href="/companies/create" class="btn btn-primary gradient">Cadastrar empresa</a>
</div>
