@push('css')
    <link rel="stylesheet" href="{{ mix('build/layouts/companies/not_company_approved_getnet.min.css') }}">
@endpush
<div class="content-error text-center" id="companies-not-approved-getnet" style="display:none;">
    <img src="{!! mix('build/global/img/empty.svg') !!}" width="250px">
    <h1 class="big gray">Você ainda não tem nenhuma empresa aprovada para transacionar!</h1>
</div>
