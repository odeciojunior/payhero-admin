@extends("layouts.master")
@section('title', '- Dashboard')
@section('content')

@section('styles')


@endsection


<div class="page">
  <div class="page-content container-fluid">
    <div class="row" data-plugin="matchHeight" data-by-row="true">
      <div class="col-xxl-12 col-lg-12">
        <!-- Widget Linearea Color -->
        <div class="card card-shadow card-responsive" id="widgetLineareaColor">
          <div class="card-block p-0">
            <div class="pt-30 p-30" style="height:calc(100% - 250px);">
              <h3 style="margin-bottom: 40px"> Saldos </h3>
              <div class="row" style="margin-bottom: 200px">

                  <div class="col-xxl-12 col-lg-4 h-p50 h-only-lg-p100 h-only-xl-p100">
                    <!-- Widget Sale Bar -->
                    <div class="card card-inverse card-shadow bg-green-600 white" id="widgetSaleBar">
                      <div class="card-block p-0">
                        <div class="pt-25 px-30">
                          <div class="row no-space">
                            <div class="col-12 text-center">
                              <p>Saldo disponível</p>
                            </div>
                            <hr>
                            <div class="col-12 text-center">
                              <p class="font-size-30 text-nowrap">R$ {!! $saldo_disponivel !!}</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- End Widget Sale Bar -->
                  </div>
        
                  <div class="col-xxl-12 col-lg-4 h-p50 h-only-lg-p100 h-only-xl-p100">
                    <!-- Widget Sale Bar -->
                    <div class="card card-inverse card-shadow bg-blue-600 white" id="widgetSaleBar">
                      <div class="card-block p-0">
                        <div class="pt-25 px-30">
                          <div class="row no-space">
                            <div class="col-12 text-center">
                              <p>Saldo a receber</p>
                            </div>
                            <div class="col-12 text-center">
                              <p class="font-size-30 text-nowrap">R$ {!! $saldo_futuro !!}</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- End Widget Sale Bar -->
                  </div>

                  <div class="col-xxl-12 col-lg-4 h-p50 h-only-lg-p100 h-only-xl-p100">
                    <!-- Widget Sale Bar -->
                    <div class="card card-inverse card-shadow bg-yellow-600 white" id="widgetSaleBar">
                      <div class="card-block p-0">
                        <div class="pt-25 px-30">
                          <div class="row no-space">
                            <div class="col-12 text-center">
                              <p>Transferido</p>
                            </div>
                            <div class="col-12 text-center">
                              <p class="font-size-30 text-nowrap">R$ {!! $saldo_transferido !!}</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- End Widget Sale Bar -->
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


@endsection

@section('scripts')

  <script src="{{ asset('adminremark/global/js/Plugin/jvectormap.js') }}"></script>
  <script src="{{ asset('adminremark/global/js/Plugin/material.js') }}"></script>
  <script src="{{ asset('adminremark/assets/examples/js/dashboard/v1.js') }}"></script>

@endsection
