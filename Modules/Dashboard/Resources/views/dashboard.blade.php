@extends("layouts.master")

@section('title', '- Dashboard')

@section('content')

<div class="page">

   <div class="page-content container">

   <div class="row align-items-center justify-content-between">

     <div class="col-lg-6">
        <h1 class="page-title">Dashboard</h1>
     </div>

     <div class="col-lg-6">
           <div class="d-lg-flex align-items-center justify-content-end">
                <div class="p-2 text-lg-right">
                  Empresa:
                </div>
                <div class="p-2 text-lg-right">
                  <select id="company" class="form-control new-select">
                      @foreach($companies as $company)
                          <option value="{!! $company['id'] !!}">{!! $company['fantasy_name'] !!}</option>
                      @endforeach
                  </select>
                </div>
          </div>
     </div>

  </div>

  <div class="clearfix"></div>

    <!-- CARDS EXTRATO -->

      <div class="row">
         <div class="col-lg-3" >
            <div class="card card-shadow bg-white">
               <div class="card-header d-flex justify-content-start align-items-center bg-white p-20">
                  <div class="font-size-14 gray-600">
                     <img src="{{ asset('modules/global/assets/img/svg/moeda-laranja.svg') }}" width="35px">
                     <span class="card-desc">Pendente</span>
                  </div>
               </div>
               <div class="card-body font-size-24 text-center d-flex align-items-topline justify-content-center">
                  <span class="moeda">R$</span> <span id="pending_money" class="text-money"></span>
               </div>
               <!-- <div class="divider"></div>
                  <div class="indices row justify-content-center align-items-center">
                    <div class="col-4">
                      <div class="d-flex justify-content-around">
                        <img src="{{ asset('modules/global/assets/img/svg/arrow.svg') }}">
                        <span class="card-p"> +24% ao dia </span>
                      </div>
                    </div>
                  
                    <div class="col-4">
                      <div class="d-flex justify-content-around">
                        <img src="{{ asset('modules/global/assets/img/svg/arrow-down.svg') }}">
                        <span class="card-p"> -2% ao dia </span>
                      </div>
                    </div>
                    
                  </div> -->
               <div class="card-bottom orange"> </div>
            </div>
         </div>
         <div class="col-lg-3" >
            <div class="card card-shadow bg-white">
               <div class="card-header d-flex justify-content-start align-items-center bg-white p-20">
                  <div class="font-size-14 gray-600">
                     <img src="{{ asset('modules/global/assets/img/svg/moeda-vermelha.svg') }}" width="35px">
                     <span class="card-desc">Antecipação</span>
                  </div>
               </div>
               <div class="card-body font-size-24 text-center d-flex align-items-topline justify-content-center" >
                  <span class="moeda">R$</span> <span id="antecipation_money" class="text-money"></span>
               </div>
               <div class="card-bottom orangered"> </div>
            </div>
         </div>
         <div class="col-lg-3" >
            <div class="card card-shadow bg-white">
               <div class="card-header d-flex justify-content-start align-items-center bg-white p-20">
                  <div class="font-size-14 gray-600">
                     <img src="{{ asset('modules/global/assets/img/svg/moeda.svg') }}" width="35px">
                     <span class="card-desc">Disponível</span>
                  </div>
               </div>
               <div class="card-body font-size-24 text-center d-flex align-items-topline justify-content-center">
                  <span class="moeda">R$</span> <span id="available_money" class="text-money"></span>
               </div>
               <div class="card-bottom green"> </div>
            </div>
         </div>
         <div class="col-lg-3" >
            <div class="card card-shadow bg-white">
               <div class="card-header d-flex justify-content-start align-items-center bg-white p-20">
                  <div class="font-size-14 gray-600">
                     <img src="{{ asset('modules/global/assets/img/svg/moeda-azul.svg') }}" width="35px">
                     <span class="card-desc">Total</span>
                  </div>
               </div>
               <div class="card-body font-size-24 text-center d-flex align-items-topline justify-content-center">
                  <span class="moeda">R$</span> <span id="total_money" class="text-money"></span>
               </div>
               <div class="card-bottom blue"> </div>
            </div>
         </div>
      </div>

      {{--  <div class="row justify-content-end align-items-center">

        <div class="p-2 text-lg-right align-items-center filtro">
          <div class="btn-group" data-toggle="buttons" role="group">
              <label class="btn btn-outline btn-primary input-pad active">
              <input type="radio" name="options" autocomplete="off" value="male" checked="">
                                    Hoje
              </label>
              <label class="btn btn-outline btn-primary input-pad">
              <input type="radio" name="options" autocomplete="off" value="female">
                                  Semana
              </label>
              <label class="btn btn-outline btn-primary input-pad">
              <input type="radio" name="options" autocomplete="off" value="n/a">
                                  Mês
              </label>
            </div>
          </div><div class="p-2 texto-filtro gray text-right align-items-center justify-content-lg-end"> 

          <span class="gray-svg"> 
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M5 19h-4v-4h4v4zm6 0h-4v-8h4v8zm6 0h-4v-13h4v13zm6 0h-4v-19h4v19zm1 2h-24v2h24v-2z"></path></svg>
          </span> 
          03 a 10 de junho de 2019

        </div>

      </div>  --}}
      </div>
   </div>
</div>


@push('scripts')
  <script src="{{ asset('assets/js/OpenLayers.js') }}"></script>
  <script src="https://js.pusher.com/4.4/pusher.min.js"></script>
  <script src="{{ asset('modules/dashboard/js/dashboard.js') }}"></script>
@endpush

@endsection
