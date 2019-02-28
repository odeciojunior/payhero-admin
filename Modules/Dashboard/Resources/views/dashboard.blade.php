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
              <div class="row" style="margin-bottom: 20px">

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

              <hr>

              <div style="width: 100%; text-align: center">
                <h3 style="margin-bottom: 20px">Últimas vendas</h3>
              </div>

              <div class="row">
                  <div class="col-xl-6 col-lg-6 col-md-6 h-p50 h-only-lg-p100 h-only-xl-p100">
                    <div id="mapa" style="height:500px">
                    </div>
                  </div>
                  <div class="col-xl-6 col-lg-6 col-md-6 h-p50 h-only-lg-p100 h-only-xl-p100">
                    <div id="tabela">
                      <table class="table table-hover table-bordered">
                          <thead>
                            <th>Hora</th>
                            <th>Projeto</th>
                            <th>Valor</th>
                            <th>Forma</th>
                          </thead>
                          <tbody id="tabela_ultimas_vendas">
                          </tbody>
                      </table>
                    </div>
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

  {{--  <script src="{{ asset('adminremark/global/js/Plugin/jvectormap.js') }}"></script>
  <script src="{{ asset('adminremark/global/js/Plugin/material.js') }}"></script>
  <script src="{{ asset('adminremark/assets/examples/js/dashboard/v1.js') }}"></script>  --}}
  <script src="{{ asset('assets/js/OpenLayers.js') }}"></script>
  {{--  <script src="https://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/build/ol.js"></script>  --}}
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

      function atualizarUltimasVendas(){

        $.ajax({
          method: "POST",
          url: "/dashboard/ultimasvendas",
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          data: { empresa: $('#select_empresas').val() },
          error: function(){
              //
          },
          success: function(data){

              $('#tabela_ultimas_vendas').html(dados_tabela);

              var dados_tabela = "";
              $.each(data, function(i, item) {
                  dados_tabela += "<tr>";
                  dados_tabela += "<td>"+data[i].data_inicio+"</td>";
                  dados_tabela += "<td>"+data[i].projeto+"</td>";
                  dados_tabela += "<td>"+data[i].valor_total_pago+"</td>";
                  dados_tabela += "<td>"+data[i].forma_pagamento+"</td>";
                  dados_tabela += "</tr>"; 

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

              $('#tabela_ultimas_vendas').html(dados_tabela);

          }

        });

      }

      atualizarUltimasVendas();

      Pusher.logToConsole = false;

      var pusher = new Pusher('339254dee7e0c0a31840', {
          cluster: 'us2',
          forceTLS: true
      });

      var channel = pusher.subscribe('channel-{!! \Auth::user()->id !!}');

      channel.bind('my-event', function(data) {
        alertPersonalizado('success','Nova venda realizada');
        clear_map_points();
        atualizarUltimasVendas();
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
