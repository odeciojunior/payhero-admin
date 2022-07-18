<div id="project-empty" class="content-error text-center" style="display:none !important;
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
    <img src="{!! mix('build/global/img/projetos.svg') !!}" width="156px">
    <h1 class="big gray">Sua empresa ainda não tem nenhuma loja!</h1>
    <p class="desc gray">Que tal criar uma primeira loja para começar a vender? </p>

    @if (auth()->user()->account_is_approved && auth()->user()->address_document_status == 3 && auth()->user()->personal_document_status == 3)
        <a href="/projects/create" class="btn btn-primary btn-floating text-center align-items-center d-flex justify-content-center text-white" style="position: relative;">
            <i class="o-add-1" aria-hidden="true"></i>
        </a>
    @else
        @if(Request::is('projects'))
            @if (auth()->user()->address_document_status == 3 && auth()->user()->personal_document_status == 3)
                <button type="button" class="new-register-open-modal-btn btn btn-primary btn-floating text-center align-items-center d-flex justify-content-center text-white" style="position: relative;">
                    <i class="o-add-1" aria-hidden="true"></i>
                </button>
            @else
                <a href="{{ env('ACCOUNT_FRONT_URL', 'https://accounts.cloudfox.net/') }}/personal-info" class="btn btn-primary btn-floating text-center align-items-center d-flex justify-content-center text-white" style="position: relative;">
                    <i class="o-add-1" aria-hidden="true"></i>
                </a>
            @endif
        @else
            <a href="/projects" class="btn btn-primary btn-floating text-center align-items-center d-flex justify-content-center text-white" style="position: relative;">
                <i class="o-add-1" aria-hidden="true"></i>
            </a>
        @endif
    @endif

</div>
@push('css')
    <link rel="stylesheet" href="{!! mix('build/layouts/projects/empty.min.css') !!}">
@endpush
