<div id="project-empty"
     class="content-error text-center"
     style="display:none !important;
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
    <img src="{!! mix('build/global/img/projetos.svg') !!}"
         width="156px">
    <h1 class="big gray">Sua empresa ainda não tem nenhuma loja!</h1>
    <p class="desc gray">Que tal criar uma primeira loja para começar a vender? </p>

    @php
        $userModel = new \Modules\Core\Entities\User();
        $user = auth()->user();
        $account_is_approved = $user->account_is_approved;
        if ($user->is_cloudfox && $user->logged_id) {
            $query = $userModel
                ::select('account_is_approved')
                ->where('id', $user->logged_id)
                ->get();
            $account_is_approved = $query[0]->account_is_approved ?? false;
        }
    @endphp

    @if ($account_is_approved && auth()->user()->address_document_status == 3)
        <button id="new-store-button"
                data-toggle="modal"
                data-target="#new-store-modal"
                class="btn btn-primary btn-floating text-center align-items-center d-flex justify-content-center text-white"
                style="position: relative;">
            <i class="o-add-1"
               aria-hidden="true"></i>
        </button>

        @if (!Request::is('projects'))
            @include('projects::create-store-modal')
        @endif
    @else
        @if (Request::is('projects'))
            @if (auth()->user()->address_document_status == 3)
                <button type="button"
                        id="new-store-button"
                        data-toggle="modal"
                        data-target="#new-store-modal"
                        data-placement="bottom"
                        title="Adicionar Loja"
                        class="new-register-open-modal-btn btn btn-primary btn-floating text-center align-items-center d-flex justify-content-center text-white"
                        style="position: relative;">
                    <i class="o-add-1"
                       aria-hidden="true"></i>
                </button>
            @else
                <a href="{{ env('ACCOUNT_FRONT_URL', 'https://accounts.azcend.com.br/') }}/personal-info"
                   class="btn btn-primary btn-floating text-center align-items-center d-flex justify-content-center text-white"
                   style="position: relative;">
                    <i class="o-add-1"
                       aria-hidden="true"></i>
                </a>
            @endif
        @else
            <a href="/projects"
               class="btn btn-primary btn-floating text-center align-items-center d-flex justify-content-center text-white"
               style="position: relative;">
                <i class="o-add-1"
                   aria-hidden="true"></i>
            </a>
        @endif
    @endif

</div>
@push('css')
    <link rel="stylesheet"
          href="{!! mix('build/layouts/projects/empty.min.css') !!}">
@endpush
