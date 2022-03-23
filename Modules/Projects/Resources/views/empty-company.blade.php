<div id="company-empty" class="content-error text-center" style="display:none !important;
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
    <img src="{!! mix('build/global/img/empty-cloud.svg') !!}" width="156px">
    <h1 class="big gray">Você ainda não tem nenhuma empresa!</h1>
    <p class="desc gray">Vamos cadastrar a primeira empresa? </p>
    <a href="{{env('ACCOUNT_FRONT_URL', 'https://accounts.cloudfox.net/')}}/companies/company-type" class="btn btn-primary btn-floating text-center align-items-center d-flex justify-content-center text-white" style="position: relative;">
        <i class="o-add-1" aria-hidden="true"></i>
    </a>
</div>
@push('css')
    <link rel="stylesheet" href="{!! mix('build/layouts/projects/empty-company.min.css') !!}">
@endpush
