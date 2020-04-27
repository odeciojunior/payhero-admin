@extends("layouts.master")
@section('content')

<div class="page">
    <div class="page-header container">
        <h1 class="page-title">Aplicativos</h1>
    </div>
    <div class="page-content container">
        <div class="row">
             <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                <div class="card" onclick="window.location.href='/apps/notazz/'" style='width:300px;'>
                    <a id="notazz-bt" href="/apps/notazz/" class="add-btn"><i id="notazz-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                    <img class="card-img-top mt-100" src="{!! asset('modules/global/img/notazz.png') !!}" alt="" align="middle">
                    <div class="card-body mt-100">
                        <h5 class="card-title">Notazz</h5>
                        <p class="card-text sm">Integre seus projetos com a Notazz </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                <div class="card" onclick="window.location.href='/apps/hotzapp/'">
                    <a id="hotzapp-bt" href="/apps/hotzapp/" class="add-btn"><i id="hotzapp-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                    <img class="card-img-top" src="{!! asset('modules/global/img/hotzapp.png') !!}" alt="">
                    <div class="card-body">
                        <h5 class="card-title">Hotzapp</h5>
                        <p class="card-text sm">Integre seus projetos com HotZapp </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                <div class="card" onclick="window.location.href='/apps/shopify'">
                    <a id="shopify-bt" href="/apps/shopify" class="add-btn"><i id="shopify-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                    <img class="card-img-top" src="{!! asset('modules/global/img/shopify.png') !!}" alt="">
                    <div class="card-body">
                        <h5 class="card-title">Shopify</h5>
                        <p class="card-text sm">Integre seus projetos com Shopify </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                <div class="card" onclick="window.location.href='/apps/convertax'" style='width:300px;'>
                    <a id="convertax-bt" href="/apps/convertax" class="add-btn"><i id="convertax-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                    <img class="card-img-top mt-100" src="{!! asset('modules/global/img/convertax.png') !!}" alt="" align="middle">
                    <div class="card-body mt-80">
                        <h5 class="card-title">ConvertaX</h5>
                        <p class="card-text sm">Integre seus projetos com ConvertaX </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                <div class="card" onclick="window.location.href='/apps/activecampaign'" style='width:300px;'>
                    <a id="activecampaign-bt" href="/apps/activecampaign" class="add-btn"><i id="activecampaign-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                    <img class="card-img-top mt-100" src="{!! asset('modules/global/img/active_campaign.png') !!}" alt="" align="middle">
                    <div class="card-body mt-80">
                        <h5 class="card-title">ActiveCampaign</h5>
                        <p class="card-text sm">Integre seus projetos com ActiveCampaign </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                <div class="card" onclick="window.location.href='/apps/whatsapp2'" style='width:300px;'>
                    <a id="whatsapp2-bt" href="/apps/whatsapp2" class="add-btn"><i id="whatsapp2-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                    <img class="card-img-top" src="{!! asset('modules/global/img/whatsapp2.png') !!}" alt="" align="middle">
                    <div class="card-body">
                        <h5 class="card-title">Whatsapp 2.0</h5>
                        <p class="card-text sm">Integre seus projetos com Whatsapp 2.0 </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                <div class="card" onclick="window.location.href='/apps/hotsac'" style='width:300px;'>
                    <a id="hotsac-bt" href="/apps/hotsac" class="add-btn"><i id="hotsac-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                    <img class="card-img-top px-10 pt-10 pb-40" src="{!! asset('modules/global/img/hotsac.png') !!}" alt="" align="middle">
                    <div class="card-body">
                        <h5 class="card-title">HotSac</h5>
                        <p class="card-text sm">Integre seus projetos com HotSac </p>
                    </div>
                </div>
            </div>
            {{--  <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                <div class="card" onclick="window.location.href='/apps/digitalmanager'" style='width:300px;'>
                    <a id="digitalmanager-bt" href="/apps/digitalmanager" class="add-btn"><i id="digitalmanager-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                    <img class="card-img-top mt-100" src="{!! asset('modules/global/img/digital_manager_guru.png') !!}" alt="" align="middle">
                    <div class="card-body mt-80">
                        <h5 class="card-title">Digital Manager Guru</h5>
                        <p class="card-text sm">Integre seus projetos com Digital Manager Guru</p>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('modules/apps/js/index.js?v=3') }}"></script>
@endpush
