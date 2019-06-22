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
                                    <p class="card-text sm">Integração seus projetos com o HotZapp</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                            <div class="card">
                                <img class="card-img-top" src="{!! asset('modules/global/assets/img/shopify.png') !!}" alt="">
                                <div class="card-body">
                                    <h5 class="card-title">Shopify</h5>
                                    <p class="card-text sm">Integração seus projetos com o Shopify </p>
                                    <a href="{!! route('shopify.index') !!}" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                            <div class="card">
                            <a href="#" class="add-btn"> <i class="icon wb-plus" aria-hidden="true"></i> </a>
                                <img class="card-img-top" src="{!! asset('modules/global/assets/img/sms.png') !!}" alt="">
                                <div class="card-body">
                                    <h5 class="card-title">SMS</h5>
                                    <p class="card-text sm">Integração para disparos de SMS</p>
                                </div>
                            </div>
                        </div>

</div>

</div>


@endsection

