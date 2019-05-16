@extends("layouts.master")
@section('title', '- Ferramentas')
@section('content')
@section('styles')
@endsection

<div class="page">
  <div class="page-content container-fluid">
    <h1 style="margin-bottom: 40px" class="page-title"> Ferramentas </h1>
      <div class="row" style="margin-bottom: 200px">
        <div class="col-xxl-12 col-lg-4 h-p50 h-only-lg-p100 h-only-xl-p100">
          <a href="{!! route('ferramentas.sms') !!}">
            <div class="card card-inverse card-shadow bg-green-600 white" id="widgetSaleBar">
              <div class="card-block p-0">
                  <div class="row no-space">
                    <div class="card-block">
                      <h4 class="card-title">SMS</h4>
                      <div class="row">
                          <div class="col-10">
                              <p class="card-text">
                                Serviço de envio de sms.
                              </p>
                          </div>
                          <div class="col-2">
                              <i class="icon wb-envelope" aria-hidden="true"></i>
                          </div>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </a>
        </div>

    </div>

  </div>
</div>


@endsection

