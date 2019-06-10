@extends("layouts.master")
@section('title', '- Dashboard')
@section('content')
@section('styles')

@endsection


<div class="page">
  <div class="page-content container">

    <h1 style="margin-bottom: 40px" class="page-title">Dashboard</h1>
  
<div class="row">

        
      <div class="col-lg-4" >
        <div class="card card-shadow bg-white"> 

          <div class="card-header d-flex justify-content-start align-items-center bg-white p-20">
          <div class="font-size-14 gray-600">
            <img src="{{ asset('assets/img/svg/moeda.svg') }}" width="35px">
              <span class="card-desc">Saldo disponível</span>
            </div>
          </div>

            <div class="font-size-24 text-center d-flex align-items-topline justify-content-center" style="margin:20px 0 15px 0">
              <span class="moeda">R$</span> <span class="text-money">{!! $available_balance !!}</span>
            </div>

            <div class="divider"></div>

            <div class="indices row justify-content-center align-items-center">
              <div class="col-4">
                <div class="d-flex justify-content-around">
                  <img src="{{ asset('assets/img/svg/arrow.svg') }}">
                  <span class="card-p"> +24% ao dia </span>
                </div>
              </div>

              <div class="col-4">
                <div class="d-flex justify-content-around">
                  <img src="{{ asset('assets/img/svg/arrow-down.svg') }}">
                  <span class="card-p"> -2% ao dia </span>
                </div>
              </div>
              
            </div>

            <div class="card-bottom"> </div>


          </div>
        </div>


        <div class="col-lg-4" >
        <div class="card card-shadow bg-white"> 

          <div class="card-header d-flex justify-content-start align-items-center bg-white p-20">
          <div class="font-size-14 gray-600">
            <img src="{{ asset('assets/img/svg/moeda-azul.svg') }}" width="35px">
              <span class="card-desc">Saldo a receber</span>
            </div>
          </div>

            <div class="font-size-24 text-center d-flex align-items-topline justify-content-center" style="margin:20px 0 15px 0">
              <span class="moeda">R$</span> <span class="text-money">{!! $future_balance !!}</span>
            </div>

            <div class="divider"></div>

            <div class="indices row justify-content-center align-items-center">
              <div class="col-4">
                <div class="d-flex justify-content-around">
                  <img src="{{ asset('assets/img/svg/arrow.svg') }}">
                  <span class="card-p"> +24% ao dia </span>
                </div>
              </div>

              <div class="col-4">
                <div class="d-flex justify-content-around">
                  <img src="{{ asset('assets/img/svg/arrow-down.svg') }}">
                  <span class="card-p"> -2% ao dia </span>
                </div>
              </div>
              
            </div>

            <div class="card-bottom blue"> </div>


          </div>
        </div>


        <div class="col-lg-4" >
        <div class="card card-shadow bg-white"> 

          <div class="card-header d-flex justify-content-start align-items-center bg-white p-20">
          <div class="font-size-14 gray-600">
            <img src="{{ asset('assets/img/svg/moeda-laranja.svg') }}" width="35px">
              <span class="card-desc">Disponível para antecipação</span>
            </div>
          </div>

            <div class="font-size-24 text-center d-flex align-items-topline justify-content-center" style="margin:20px 0 15px 0">
              <span class="moeda">R$</span> <span class="text-money">{!! $future_balance !!}</span>
            </div>

            <div class="divider"></div>

            <div class="indices row justify-content-center align-items-center">
              <div class="col-4">
                <div class="d-flex justify-content-around">
                  <img src="{{ asset('assets/img/svg/arrow.svg') }}">
                  <span class="card-p"> +24% ao dia </span>
                </div>
              </div>

              <div class="col-4">
                <div class="d-flex justify-content-around">
                  <img src="{{ asset('assets/img/svg/arrow-down.svg') }}">
                  <span class="card-p"> -2% ao dia </span>
                </div>
              </div>
              
            </div>

            <div class="card-bottom orange"> </div>


          </div>
        </div>
        
        

</div>



    <div class="row" style="margin-top: 20px">
      <div class="col-xl-3 col-md-6 info-panel">
        <div class="card card-shadow">
          <div class="card-block p-20" style="background-image: linear-gradient(#e6774c, #f92278);">
            <button type="button" class="btn btn-floating btn-sm btn-primary">
              <i class="icon wb-user"></i>
            </button>
            <span class="ml-15 font-weight-400" style="color: white">NÍVEL ATUAL</span><hr>
            <div class="row" style="color: white">
              <div class="col-6 content-text mb-0 text-center" style="border-right: 2px solid;">
                <span class="font-size-20 font-weight-50"></span>
                <span class="font-size-20 font-weight-50">1</span> - Iniciante
              </div>
              <div class="col-6 content-text mb-0 text-center">
                <span class="font-size-20 font-weight-50"></span>
                FoxCoins - 88
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 info-panel">
        <div class="card card-shadow">
          <div class="card-block bg-white p-20">
            <button type="button" class="btn btn-floating btn-sm btn-warning">
              <i class="icon wb-shopping-cart"></i>
            </button>
            <span class="ml-15 font-weight-400">PEDIDOS HOJE</span><hr>
            <div class="content-text mb-0 text-center">
              <span class="font-size-20 font-weight-50">{!! $sales_count !!}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 info-panel">
        <div class="card card-shadow">
          <div class="card-block bg-white p-20">
            <button type="button" class="btn btn-floating btn-sm btn-danger">
              <i class="icon wb-payment"></i>
            </button>
            <span class="ml-15 font-weight-400">RENDA</span><hr>
            <div class="content-text mb-0">
              <span class="font-size-20 font-weight-50">R$ {!! $daily_balance !!}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 info-panel">
        <div class="card card-shadow">
          <div class="card-block bg-white p-20">
            <button type="button" class="btn btn-floating btn-sm btn-success">
              <i class="icon wb-eye"></i>
            </button>
            <span class="ml-15 font-weight-400">ACESSOS</span><hr>
            <div class="content-text mb-0 text-center">
              <span class="font-size-20 font-weight-50">{!! $checkouts !!}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div style="width: 100%; text-align: center">
      <h3 style="margin-bottom: 20px" class="page-title">Últimas vendas</h3>
    </div>

    <!-- <div class="row">

      <div class="col-xl-6 col-lg-6 col-md-6 h-p50 h-only-lg-p100 h-only-xl-p100">
        <div id="mapa" style="height:440px">
        </div>
      </div>
      <div class="col-xl-6 col-lg-6 col-md-6 h-p50 h-only-lg-p100 h-only-xl-p100">
        <div id="tabela" class="card card-shadow">
          <table class="table table-hover text-center">
              <thead>
                <tr>
                  <th style='vertical-align: middle'>Data</th>
                  <th style='vertical-align: middle'>Projeto</th>
                  <th style='vertical-align: middle'>Valor</th>
                  <th style='vertical-align: middle'>Forma</th>
                </tr>
              </thead>
              <tbody id="last_sales_table">
              </tbody>
          </table>
        </div>
      </div>
    </div> -->
  </div>
</div>
 

@endsection

@section('scripts')

  <script src="{{ asset('assets/js/OpenLayers.js') }}"></script>
  <script src="https://js.pusher.com/4.4/pusher.min.js"></script>

  <script>

    $(document).ready(function(){

      var map = new ol.Map({
        target: 'mapa',
        layers: [
          new ol.layer.Tile({
            source: new ol.source.OSM()
          })
        ],
        view: new ol.View({
          center: ol.proj.fromLonLat([-47.7, -23.7]),
          zoom: 3
        })
      });

      var vectorLayers = new Array();

      function add_map_point(lat, lng) {
        var vectorLayer = new ol.layer.Vector({
          source:new ol.source.Vector({
            features: [new ol.Feature({
              geometry: new ol.geom.Point(ol.proj.transform(
                [parseFloat(lng), parseFloat(lat)], 'EPSG:4326', 'EPSG:3857')),
            })]
          }),
          style: new ol.style.Style({
            image: new ol.style.Icon({
              anchor: [0.5, 35],
              anchorXUnits: "fraction",
              anchorYUnits: "pixels",
              src: "/assets/img/marker.png"
            })
          })
        });

        map.addLayer(vectorLayer);
        vectorLayers.push(vectorLayer);

      }

      function clear_map_points(){
        vectorLayers.forEach((vectorLayer) => {
          var features = vectorLayer.getSource().getFeatures();
          features.forEach((feature) => {
              vectorLayer.getSource().removeFeature(feature);
          });
        });
        vectorLayers.length = 0;
      }

      function updateLastSales(){

        $.ajax({
          method: "POST",
          url: "/dashboard/lastsales",
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          data: { empresa: $('#select_empresas').val() },
          error: function(){
              //
          },
          success: function(data){

              $('#last_sales_table').html(table_data);

              var table_data = "";
              $.each(data, function(i, item) {
                  table_data += "<tr>";
                  table_data += "<td>"+data[i].start_date+"</td>";
                  table_data += "<td>"+data[i].project+"</td>";
                  table_data += "<td>"+data[i].total_paid_value+"</td>";
                  table_data += "<td>"+data[i].payment_form+"</td>";
                  table_data += "</tr>"; 

                  if(data[i].ip != null){
                    $.ajax({
                        url : "https://ipapi.co/"+data[i].ip+"/json",
                        type : "GET",
                        success : function(response) {
                          add_map_point(response.latitude, response.longitude);
                        }
            
                    });
                  }
              });

              $('#last_sales_table').html(table_data);

          }

        });

      }

      updateLastSales();

      Pusher.logToConsole = false;

      var pusher = new Pusher('339254dee7e0c0a31840', {
          cluster: 'us2',
          forceTLS: true
      });

      var channel = pusher.subscribe('channel-{!! \Auth::user()->id !!}');

      channel.bind('my-event', function(data) {
        alertPersonalizado('success','Nova venda realizada');
        clear_map_points();
        updateLastSales();
      });

      function alertPersonalizado(tipo, mensagem){

          swal({
              position: 'bottom',
              type: tipo,
              toast: 'true',
              title: mensagem,
              showConfirmButton: false,
              timer: 6000
          });
        }


    });

  </script>
@endsection
