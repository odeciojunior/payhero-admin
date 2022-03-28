@push('css')
    <link rel="stylesheet" href="{!! mix('build/layouts/companies/empty.min.css') !!}">
@endpush
<div class="content-error text-center" id="empty-companies-error" style="display:none">
    <img src="{!! mix('build/global/img/empty.svg') !!}" width="250px">
    <h1 class="big gray">Você ainda não tem nenhuma empresa!</h1>
    <p class="desc gray">Vamos cadastrar a primeira empresa? </p>
    <a href="" class="btn btn-primary redirect-to-accounts" data-url-value="/companies">Cadastrar empresa</a>
</div>
