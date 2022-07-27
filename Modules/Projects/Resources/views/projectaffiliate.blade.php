@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{ mix('build/layouts/projects/projectaffiliate.min.css') }}">
@endpush

@section('content')

    <!-- Page -->
    <div class="page">
        <div style="" class="page-header container">
            <h1 class="page-title" style="min-height: 28px">
                <a class="gray" href="/projects" style="outline: none">
                    <span class="o-arrow-right-1 font-size-30 ml-2 gray" aria-hidden="true"></span>
                    Minhas Lojas
                </a>
            </h1>
        </div>
        <div class="page-content container page-project">
            <div class="mt-15">
                <!-- Painel de informações gerais -->
                <div class="card">
                    <div class="row">
                        <div class="col-md-3">
                            <img id="show-photo" class="card-img" src="" alt="">
                        </div>
                        <div class="col-md-9 pl-10">
                            <div class="card-body">
                                <div class="row justify-content-between align-items-baseline">
                                    <div class="col-md-12">
                                        <!-- <div class="row row-flex row-title">
                                            <h4 class="title-pad mr-5"></h4>
                                            <span id="show-status"
                                                  class="text-white details-text md p-2 pr-4 pl-4 badge-pill"></span>
                                        </div> -->
                                        <div class="row d-flex row-title justify-content-between pt-0 align-items-start">
                                            <div class="col-9 title-pad s-title pl-10 pl-sm-0"></div>
                                            <div id="show-status" class="col-2 text-white text-center details-text my-5 ml-10 ml-sm-0 badge-pill badge-success"></div>
                                        </div>
                                        <small class="card-text gray font-size-14" id="created_at"></small>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-md-6'>
                                        <h5 class=""><strong> Descrição </strong></h5>
                                        <p id="show-description" class="card-text sm font-size-14"></p>
                                        <h5 class=""><strong> Produtor </strong></h5>
                                        <p id="show-producer" class="card-text sm font-size-14"></p>
                                        <h5 class=""><strong> Comissão </strong></h5>
                                        <p id="show-commission" class="card-text sm font-size-14"></p>
                                    </div>
                                    <div class='col-md-6'>
                                        <h5 class=""><strong> Dias para liberar dinheiro </strong></h5>
                                        <p class="card-text sm font-size-14"><span id='show-billet-release'></span></p>

                                        <h5 class=""><strong> Duração do cookie </strong></h5>
                                        <p id="show-cookie-duration" class="card-text sm font-size-14"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nav-tabs-horizontal" data-plugin="tabs">
                    <ul class="nav nav-tabs nav-tabs-line" role="tablist" style="border-bottom-color: transparent;">
                        <li class="nav-item tab_pixels-panel" role="presentation">
                            <a id="tab_pixels" class="nav-link active" data-toggle="tab" href="#tab_pixels-panel"
                               aria-controls="tab_pixels" role="tab">Pixels
                            </a>
                        </li>
                        <li class="nav-item tab_links-panel" role="presentation">
                            <a id="tab_links" class="nav-link" data-toggle="tab" href="#tab_links-panel"
                               aria-controls="tab_links" role="tab">
                                Links
                            </a>
                        </li>
{{--                        <li class="nav-item tab_settings_affiliate-panel" role="presentation"--}}
{{--                            style="margin-left: auto;margin-right: 10px">--}}
{{--                            <a id="tab_settings_affiliate" class="nav-link"--}}
{{--                               data-toggle="tab" href="#tab_settings_affiliate-panel"--}}
{{--                               aria-controls="tab_settings_affiliate" role="tab">--}}
{{--                                <img height="15" src="{{ mix('build/global/img/svg/settings.svg') }}"/>--}}
{{--                            </a>--}}
{{--                        </li>--}}
                    </ul>
                </div>
            </div>
            <div class="shadow" data-plugin="matchHeight" style="margin-top: 2px;">
                <div class="tab-content">
                    <div class="tab-content">
                        <!-- Painel de Pixels -->
                        <div class="tab-pane active" id="tab_pixels-panel" role="tabpanel">
                            @include('pixels::affiliates')
                        </div>
                        <!--- Painel de Planos -->
                        <div class="tab-pane" id="tab_links-panel" role="tabpanel">
                            @include('affiliates::links')
                        </div>
                        <!--- Painel de Configurações -->
{{--                        <div class="tab-pane" id="tab_settings_affiliate-panel" role="tabpanel">--}}
{{--                            @include('projects::editprojectaffiliate')--}}
{{--                        </div>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ mix('build/layouts/projects/projectaffiliate.min.js') }}"></script>
    @endpush
@endsection

