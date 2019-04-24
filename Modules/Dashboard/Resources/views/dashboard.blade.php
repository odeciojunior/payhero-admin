@extends("layouts.master")
@section('title', '- Dashboard')
@section('content')

@section('styles')

@endsection

<div class="page">
  <div class="page-content container-fluid">

    <div class="row">
      <div class="col-sm-12 col-md-6 col-lg-6">
        <div class="card card-inverse card-shadow bg-green-600 white">
          <div class="card-block p-0">
            <div class="pt-25 px-30">
              <div class="row no-space">
                <div class="col-12 text-center">
                  <p>Saldo disponível</p>
                </div><hr>
                <div class="col-12 text-center">
                  <p class="font-size-30 text-nowrap">R$ {!! $saldo_disponivel !!}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-6 col-lg-6">
        <div class="card card-inverse card-shadow bg-blue-600 white">
          <div class="card-block p-0">
            <div class="pt-25 px-30">
              <div class="row no-space">
                <div class="col-12 text-center">
                  <p>Saldo a receber</p>
                </div><hr>
                <div class="col-12 text-center">
                  <p class="font-size-30 text-nowrap">R$ {!! $saldo_futuro !!}</p>
                </div>
              </div>
            </div>
          </div>
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
              <span class="font-size-20 font-weight-50">7</span>
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
              <span class="font-size-20 font-weight-50">R$ 825,90</span>
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
              <span class="font-size-20 font-weight-50">128</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div style="width: 100%; text-align: center">
      <h3 style="margin-bottom: 20px">Últimas vendas</h3>
    </div>

    <div class="row">

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
              <tbody id="tabela_ultimas_vendas">
              </tbody>
          </table>
        </div>
      </div>
    </div>
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
