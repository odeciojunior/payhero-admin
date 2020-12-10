@extends("layouts.master")
@section('content')
    <style>
        .card-img-controll {
            max-height: 250px;
        }

    </style>

    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Aplicativos</h1>
        </div>
        <div class="page-content container">
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch div-notazz-integration">
                    <div class="card" onclick="window.location.href='/apps/notazz/'" style='width:300px;'>
                        <a id="notazz-bt" href="/apps/notazz/" class="add-btn">
                            <i id="notazz-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                        <img class="card-img-top card-img-controll" src="{!! asset('modules/global/img/notazz.png') !!}" alt="" align="middle">
                        <div class="card-body">
                            <h5 class="card-title">Notazz</h5>
                            <p class="card-text sm">Integre seus projetos com a Notazz </p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                    <div class="card" onclick="window.location.href='/apps/hotzapp/'">
                        <a id="hotzapp-bt" href="/apps/hotzapp/" class="add-btn">
                            <i id="hotzapp-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                        <img class="card-img-top card-img-controll" src="{!! asset('modules/global/img/hotzapp.png') !!}" alt="">
                        <div class="card-body">
                            <h5 class="card-title">Hotzapp</h5>
                            <p class="card-text sm">Integre seus projetos com HotZapp </p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                    <div class="card" onclick="window.location.href='/apps/shopify'">
                        <a id="shopify-bt" href="/apps/shopify" class="add-btn">
                            <i id="shopify-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                        <img class="card-img-top card-img-controll" src="{!! asset('modules/global/img/shopify.png') !!}" alt="">
                        <div class="card-body">
                            <h5 class="card-title">Shopify</h5>
                            <p class="card-text sm">Integre seus projetos com Shopify </p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                    <div class="card" onclick="window.location.href='/apps/convertax'" style='width:300px;'>
                        <a id="convertax-bt" href="/apps/convertax" class="add-btn">
                            <i id="convertax-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                        <img class="card-img-top card-img-controll" src="{!! asset('modules/global/img/convertax.png') !!}" alt="" align="middle">
                        <div class="card-body">
                            <h5 class="card-title">ConvertaX</h5>
                            <p class="card-text sm">Integre seus projetos com ConvertaX </p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                    <div class="card" onclick="window.location.href='/apps/activecampaign'" style='width:300px;'>
                        <a id="activecampaign-bt" href="/apps/activecampaign" class="add-btn">
                            <i id="activecampaign-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                        <img class="card-img-top card-img-controll" src="{!! asset('modules/global/img/active_campaign.png') !!}" alt="" align="middle">
                        <div class="card-body">
                            <h5 class="card-title">ActiveCampaign</h5>
                            <p class="card-text sm">Integre seus projetos com ActiveCampaign </p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                    <div class="card" onclick="window.location.href='/apps/whatsapp2'" style='width:300px;'>
                        <a id="whatsapp2-bt" href="/apps/whatsapp2" class="add-btn">
                            <i id="whatsapp2-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                        <img class="card-img-top card-img-controll" src="{!! asset('modules/global/img/whatsapp2.png') !!}" alt="" align="middle">
                        <div class="card-body">
                            <h5 class="card-title">Whatsapp 2.0</h5>
                            <p class="card-text sm">Integre seus projetos com Whatsapp 2.0 </p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                    <div class="card" onclick="window.location.href='/apps/hotsac'" style='width:300px;'>
                        <a id="hotsac-bt" href="/apps/hotsac" class="add-btn">
                            <i id="hotsac-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                        <img class="card-img-top card-img-controll px-10 pt-10 pb-40" src="{!! asset('modules/global/img/hotsac.png') !!}" alt="" align="middle">
                        <div class="card-body">
                            <h5 class="card-title">HotSac</h5>
                            <p class="card-text sm">Integre seus projetos com HotSac </p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                    <div class="card" onclick="window.location.href='/apps/reportana'" style='width:300px;'>
                        <a id="reportana-bt" href="/apps/reportana" class="add-btn">
                            <i id="reportana-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                        <img class="card-img-top card-img-controll" src="{!! asset('modules/global/img/reportana.png') !!}" alt="" align="middle">
                        <div class="card-body">
                            <h5 class="card-title">Reportana</h5>
                            <p class="card-text sm">Integre seus projetos com Reportana </p>
                        </div>
                    </div>
                </div>
                @if(auth()->user()->id == 24)
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card" onclick="window.location.href='/apps/unicodrop'" style='width:300px;'>
                            <a id="unicodrop-bt" href="/apps/unicodrop" class="add-btn">
                                <i id="unicodrop-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                            <img class="card-img-top card-img-controll" src="{!! asset('modules/global/img/unicodrop.png') !!}" alt="" align="middle">
                            <div class="card-body">
                                <h5 class="card-title">Unicodrop</h5>
                                <p class="card-text sm">Integre seus projetos com Unicodrop </p>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                    <div class="card" onclick="window.location.href='/apps/smartfunnel'" style='width:300px;'>
                        <a id="smartfunnel-bt" href="/apps/smartfunnel" class="add-btn">
                            <i id="smartfunnel-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                        <img class="card-img-top card-img-controll" src="{!! asset('modules/global/img/smartfunnel.png') !!}" alt="" align="middle">
                        <div class="card-body">
                            <h5 class="card-title">Smart Funnel</h5>
                            <p class="card-text sm">Integre seus projetos com Smart Funnel </p>
                        </div>
                    </div>
                </div>
                {{--  <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                    <div class="card" onclick="window.location.href='/apps/digitalmanager'" style='width:300px;'>
                        <a id="digitalmanager-bt" href="/apps/digitalmanager" class="add-btn"><i id="digitalmanager-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                        <img class="card-img-top card-img-controll" src="{!! asset('modules/global/img/digital_manager_guru.png') !!}" alt="" align="middle">
                        <div class="card-body">
                            <h5 class="card-title">Digital Manager Guru</h5>
                            <p class="card-text sm">Integre seus projetos com Digital Manager Guru</p>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
        @endsection

        @push('scripts')
            <script src="{{ asset('modules/apps/js/index.js?v=' . random_int(100, 10000)) }}"></script>
    @endpush
