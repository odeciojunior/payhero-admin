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

        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
            <div class="card">
                <a href="projetcasasd" class="add-btn"> <i class="icon wb-plus" aria-hidden="true"></i> </a>
                <img class="card-img-top" src="{!! asset('modules/global/assets/img/hotzapp.png') !!}" alt="">
                <div class="card-body">
                    <h5 class="card-title">Hotzapp</h5>
                    <p class="card-text sm">Integre seus projetos com HotZapp </p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
            <div class="card">
            <a href="/apps/shopify" class="add-btn added"> <i class="icon wb-check" aria-hidden="true"></i> </a>
                <img class="card-img-top" src="{!! asset('modules/global/assets/img/shopify.png') !!}" alt="">
                <div class="card-body">
                    <h5 class="card-title">Shopify</h5>
                    <p class="card-text sm">Integre seus projetos com Shopify </p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
            <div class="card">
            <a href="#" class="add-btn"> <i class="icon wb-plus" aria-hidden="true"></i> </a>
                <img class="card-img-top" src="{!! asset('modules/global/assets/img/sms.png') !!}" alt="">
                <div class="card-body">
                    <h5 class="card-title">SMS</h5>
                    <p class="card-text sm">Integre seus projetos com SMS</p>
                </div>
            </div>
        </div>

    </div>

</div>


@endsection

