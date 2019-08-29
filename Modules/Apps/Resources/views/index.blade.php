@extends("layouts.master")
@section('title', '- Aplicativos')
@section('content')
@section('styles')
@endsection

<div class="page">
    <div class="page-header container">
        <h1 class="page-title">Aplicativos</h1>
    </div>
    <div class="page-content container">
        <div class="row">
{{--            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">--}}
{{--                <div class="card" onclick="window.location.href='/apps/notazz/'" style='width:300px;'>--}}
{{--                    @if($notazzIntegrations >0)--}}
{{--                        <a href="/apps/notazz/" class="add-btn added"><i class="icon wb-check" aria-hidden="true"></i>--}}
{{--                        </a>--}}
{{--                    @else--}}
{{--                        <a href="/apps/notazz/" class="add-btn"><i class="icon wb-plus" aria-hidden="true"></i></a>--}}
{{--                    @endif--}}
{{--                    <img class="card-img-top mt-100" src="{!! asset('modules/global/img/notazz.png') !!}" alt="" align="middle">--}}
{{--                    <div class="card-body mt-100">--}}
{{--                        <h5 class="card-title">Notazz</h5>--}}
{{--                        <p class="card-text sm">Integre seus projetos com a Notazz </p>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                <div class="card" onclick="window.location.href='/apps/hotzapp/'">
                    @if($hotzappIngrations >0)
                        <a href="/apps/hotzapp/" class="add-btn added"><i class="icon wb-check" aria-hidden="true"></i>
                        </a>
                    @else
                        <a href="/apps/hotzapp/" class="add-btn"><i class="icon wb-plus" aria-hidden="true"></i></a>
                    @endif
                    <img class="card-img-top" src="{!! asset('modules/global/img/hotzapp.png') !!}" alt="">
                    <div class="card-body">
                        <h5 class="card-title">Hotzapp</h5>
                        <p class="card-text sm">Integre seus projetos com HotZapp </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                <div class="card" onclick="window.location.href='/apps/shopify'">
                    @if($shopifyIntegrations >0)
                        <a href="/apps/shopify" class="add-btn added"><i class="icon wb-check" aria-hidden="true"></i>
                        </a>
                    @else
                        <a href="/apps/shopify" class="add-btn"><i class="icon wb-plus" aria-hidden="true"></i></a>
                    @endif
                    <img class="card-img-top" src="{!! asset('modules/global/img/shopify.png') !!}" alt="">
                    <div class="card-body">
                        <h5 class="card-title">Shopify</h5>
                        <p class="card-text sm">Integre seus projetos com Shopify </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                <div class="card" onclick="window.location.href='/apps/convertax'" style='width:300px;'>
                    @if($convertaxIntegrations >0)
                        <a href="/apps/convertax" class="add-btn added"><i class="icon wb-check" aria-hidden="true"></i>
                        </a>
                    @else
                        <a href="/apps/convertax" class="add-btn"><i class="icon wb-plus" aria-hidden="true"></i></a>
                    @endif
                    <img class="card-img-top mt-100" src="https://convertax.com.br/rafaelfiles/logo.png" alt="" align="middle">
                    <div class="card-body mt-80">
                        <h5 class="card-title">ConvertaX</h5>
                        <p class="card-text sm">Integre seus projetos com ConvertaX </p>
                    </div>
                </div>
            </div>
            {{--  <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                <div class="card">
                <a href="#" class="add-btn"> <i class="icon wb-plus" aria-hidden="true"></i> </a>
                    <img class="card-img-top" src="{!! asset('modules/global/img/sms.png') !!}" alt="">
                    <div class="card-body">
                        <h5 class="card-title">SMS</h5>
                        <p class="card-text sm">Integre seus projetos com SMS</p>
                    </div>
                </div>
            </div>  --}}
        </div>
    </div>
@endsection

