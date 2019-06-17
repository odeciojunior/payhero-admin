@extends("layouts.master")

@section('content')
<style>
  tbody tr:hover {
    transform: scale(1.03);
  }
  </style>
  <!-- Page -->
  <div class="page">
    

    <div class="page-header container">
        <div class="row align-items-center justify-content-between">
            <div class="col-6">
              <h1> Vendas </h1>
            </div>
            <div class="col-6 text-right">
                <div class="d-flex justify-content-end align-items-center">
                  <div class="p-2 align-items-center">
                    <i class="icon wb-calendar icon-results" aria-hidden="true"></i> <span class="text-result"> RESULTADOS DE 15 A 26 DE MAIO DE 2019 </span>
                  </div>

                  <div class="p-2 align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg"class="icon-download" width="20" height="20" viewBox="0 0 24 24"><path d="M8 20h3v-5h2v5h3l-4 4-4-4zm11.479-12.908c-.212-3.951-3.473-7.092-7.479-7.092s-7.267 3.141-7.479 7.092c-2.57.463-4.521 2.706-4.521 5.408 0 3.037 2.463 5.5 5.5 5.5h3.5v-2h-3.5c-1.93 0-3.5-1.57-3.5-3.5 0-2.797 2.479-3.833 4.433-3.72-.167-4.218 2.208-6.78 5.567-6.78 3.453 0 5.891 2.797 5.567 6.78 1.745-.046 4.433.751 4.433 3.72 0 1.93-1.57 3.5-3.5 3.5h-3.5v2h3.5c3.037 0 5.5-2.463 5.5-5.5 0-2.702-1.951-4.945-4.521-5.408z"/></svg>                      <div class="btn-group" role="group">
                          <button type="button" class="btn btn-round btn-default btn-outline btn-pill-left">.XLS</button>
                          <button type="button" class="btn btn-round btn-default btn-outline btn-pill-right">.CSV</button>
                      </div>
                  </div>
                </div>
            </div>
          </div>
      </div>

    <div class="page-content container">

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
                  <span class="moeda">R$</span> <span class="text-money">1.000,00</span>
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
                  <span class="moeda">R$</span> <span class="text-money">1.000,00</span>
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
                  <span class="moeda">R$</span> <span class="text-money">1.000,00</span>
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
                  <span class="moeda">R$</span> <span class="text-money">1.000,00</span>
               </div>
               <div class="card-bottom blue"> </div>
            </div>
         </div>
      </div>



      <div class="row justify-content-between align-items-center">
        <div class="col-lg-6">
          <div class="row align-items-center justify-content-start">
              <div class="col">
              <div class="form-group">
                                        <div class="input-search">
                                            <i class="input-search-icon wb-search" aria-hidden="true"></i>
                                            <input type="text" class="form-control input-pad" name="" placeholder="Digite sua pesquisa... ">
                                        </div>
                                    </div>
              </div>

              <div class="col">
                
                <select name="quantidade" id="" class="form-control select-pad">
                  <option value="">50 itens por página</option>
                  <option value="">100 itens por página</option>

                </select>
                
              </div>


          </div>
        </div>
        <div class="col-lg-6 text-right">
          <a id="filtros" class="text-filtros"><svg xmlns="http://www.w3.org/2000/svg" class="icon-filtro" width="14" height="14" viewBox="0 0 24 24"><path d="M19.479 2l-7.479 12.543v5.924l-1-.6v-5.324l-7.479-12.543h15.958zm3.521-2h-23l9 15.094v5.906l5 3v-8.906l9-15.094z"/></svg>
           Filtros</a>
        </div>
      </div>

      <div class="fixhalf"></div>

    
      <div id="div_filtros" class="panel pt-30 p-30" style="display:none">
        <div class="row">
          <div class="col-3">
            <label for="projeto">Projeto</label>
            <select id="projeto" class="form-control">
              <option value="">Todos projetos</option>
              @foreach($projetos as $projeto)
                <option value="{!! $projeto['id'] !!}">{!! $projeto['nome'] !!}</option>
              @endforeach
            </select>
          </div>
          <div class="col-3">
            <label for="forma">Forma de pagamento</label>
            <select id="forma" class="form-control">
              <option value="">Boleto e cartão de crédito</option>
              <option value="credit card">Cartão de crédito</option>
              <option value="boleto">Boleto</option>
            </select>
          </div>
          <div class="col-3">
            <label for="status">Status</label>
            <select id="status" class="form-control">
              <option value="">Todos status</option>
              <option value="paid">Aprovado</option>
              <option value="waiting_payment">Aguardando pagamento</option>
              <option value="chargedback">Estornada</option>
            </select>
          </div>
          <div class="col-3">
            <label for="comprador">Nome do cliente</label>
            <input id="comprador" class="form-control" placeholder="cliente">
          </div>
        </div>
        <div class="row" style="margin-top:30px">
          <div class="col-3">
            <label for="data_inicial">Data inicial</label>
            <input id="data_inicial" class="form-control" type="date">
          </div>
          <div class="col-3">
            <label for="data_final">Data final</label>
            <input id="data_final" class="form-control" type="date">
          </div>
          <div class="col-4">
          </div>
          <div class="col-2">
            <button id="bt_filtro" class="btn btn-primary" style="margin-top: 30px">Aplicar filtros</button>
          </div>
        </div>
      </div>

      <div class="panel pt-10 p-10" style="min-height: 300px">
        <div class="page-invoice-table table-responsive">

          <table id="tabela_vendas" class="table text-right table-vendas" style="width:100%;">
            <thead style="text-align:center" style="margin-bottom:8px">
              <tr>
                <th style='vertical-align: middle' class="table-title">Transação</th>
                <th style='vertical-align: middle' class="table-title">Projeto</th>
                <th style='vertical-align: middle' class="table-title">Descrição</th>
                <th style='vertical-align: middle' class="table-title">Cliente</th>
                <th style='vertical-align: middle' class="table-title">Forma</th>
                <th style='vertical-align: middle' class="table-title">Status</th>
                <th style='vertical-align: middle' class="table-title">Data</th>
                <th style='vertical-align: middle' class="table-title">Pagamento</th>
                <th style='vertical-align: middle' class="table-title">Comissão</th>
                <th style='vertical-align: middle' class="table-title" width="80px;"> &nbsp; </th>
              </tr>
            </thead>
            <tbody id="dados_tabela">

            </tbody>
          </table>
        </div>

        <ul id="pagination" class="pagination-sm m-30" style="margin-top:10px;position:relative;float:right"></ul>


        <!-- Modal detalhes da venda-->
        <div class="modal fade example-modal-lg" id="modal_detalhes" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
          <div class="modal-dialog modal-simple modal-sidebar modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                <h4 id="modal_venda_titulo" class="modal-title" style="width: 100%; text-align:center"></h4>
              </div>
              <div id="modal_venda_body" class="modal-body">

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal -->

        <!-- Modal estornar venda-->
        <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_estornar" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
          <div class="modal-dialog modal-simple">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                <h4 id="modal_estornar_titulo" class="modal-title" style="width: 100%; text-align:center">Estornar venda?</h4>
              </div>
              <div id="modal_estornar_body" class="modal-body">

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-success bt_estornar_venda">Confirmar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal -->

      </div>
    </div>
  </div>

  <script>

    $(document).ready( function(){

      atualizar();

      $("#filtros").on("click", function(){
        if($("#div_filtros").is(":visible")){
          $("#div_filtros").hide(700);
        }
        else{
          $("#div_filtros").show(700);
        }
      });

      $("#bt_filtro").on("click", function(){
        atualizar();
      });

      function atualizar(link = null){

        $('#dados_tabela').html("<tr class='text-center'><td colspan='11'> Carregando...</td></tr>");

        if(link == null){
          link = '/relatorios/getvendas?' + 'projeto='+ $("#projeto").val() + '&forma='+ $("#forma").val()+ '&status='+ $("#status").val() + '&comprador='+ $("#comprador").val() + '&data_inicial='+ $("#data_inicial").val() + '&data_final='+ $("#data_final").val();
        }
        else{
          link = '/relatorios/getvendas'+ link + '&projeto='+ $("#projeto").val() + '&forma='+ $("#forma").val()+ '&status='+ $("#status").val() + '&comprador='+ $("#comprador").val() + '&data_inicial='+ $("#data_inicial").val() + '&data_final='+ $("#data_final").val();
        }

        $.ajax({
            method: "GET",
            url: link,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function(){
              //
            },
            success: function(response){
              $('#dados_tabela').html('');

              $.each(response.data, function(index, value){
                dados = '';
                dados += '<tr>';
                dados += "<td class='text-left' style='vertical-align: middle'>"+value.id+"</td>";
                dados += "<td style='vertical-align: middle' class='text-center'>"+value.projeto+"</td>";
                dados += "<td style='vertical-align: middle' class='text-center'>"+value.produto+"</td>";
                dados += "<td style='vertical-align: middle' class='text-center'>"+value.comprador+"</td>";

                if(value.forma == 'boleto'){
                  dados += "<td style='vertical-align: middle' class='text-center'><img src='/modules/global//assets/img/boleto.jpeg' style='width: 60px'></td>";
                }else{
                  if(value.brand == 'mastercard'){
                    dados += "<td style='vertical-align: middle' class='text-center'><img src='/modules/global//assets/img/master.1.svg' style='width: 60px'></td>";
                  }
                  else if(value.brand == 'visa'){
                    dados += "<td style='vertical-align: middle' class='text-center'><img src='/modules/global//assets/img/visa.svg' style='width: 60px'></td>";
                  }
                  else{
                    dados += "<td style='vertical-align: middle' class='text-center'><img src='/modules/global//assets/img/cartao.jpg' style='width: 60px'></td>";
                  }
                }

                if(value.status == 'CO' || value.status == 'paid'){
                  dados += "<td style='vertical-align: middle' class='text-center'><span class='badge badge-success'>Aprovada</span></td>";
                } else if(value.status == 'CA' || value.status == 'refused'){
                  dados += "<td style='vertical-align: middle' class='text-center'><span class='badge badge-danger'>Recusada</span></td>";
                }else if(value.status == 'chargedback' || value.status == 'refunded'){
                  dados += "<td style='vertical-align: middle' class='text-center'><span class='badge badge-secondary'>Estornada</span></td>";
                }else if(value.status == 'PE' || value.status == 'waiting_payment'){
                  dados += "<td style='vertical-align: middle' class='text-center'><span class='badge badge-primary'>Pendente</span></td>";
                }else{
                  dados += "<td style='vertical-align: middle' class='text-center'><span class='badge badge-primary'>"+value.status+"</span></td>";
                }

                dados += "<td style='vertical-align: middle' class='text-center'>"+value.data_inicio+"</td>";
                dados += "<td style='vertical-align: middle' class='text-center'>"+value.data_finalizada+"</td>";
                dados += "<td style='vertical-align: middle;white-space: nowrap' class='text-center'><b>"+value.total_pago+"</b></td>";
                dados += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger detalhes_venda' venda='"+value.id+"' data-target='#modal_detalhes' data-toggle='modal' type='button'><i class='icon wb-eye' aria-hidden='true'></i></button></td>";
                dados += '</tr>';
                $("#dados_tabela").append(dados);

              });
              if(response.data == ''){
                $('#dados_tabela').html("<tr class='text-center'><td colspan='11' style='height: 70px;vertical-align: middle'> Nenhuma venda encontrada</td></tr>");
              }
              pagination(response);

              var id_venda = '';

              $('.detalhes_venda').unbind('click');

              $('.detalhes_venda').on('click', function() {

                  var venda = $(this).attr('venda');

                  $('#modal_venda_titulo').html('Detalhes da venda ' + venda + '<br><hr>');

                  $('#modal_venda_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");

                  var data = { sale_id : venda };

                  $.post('/relatorios/venda/detalhe', data)
                  .then( function(response, status){

                      $('#modal_venda_body').html(response);
                  });
              });

              $('.estornar_venda').unbind('click');

              $('.estornar_venda').on('click', function() {

                  id_venda = $(this).attr('venda');

                  $('#modal_estornar_titulo').html('Estornar venda #' + id_venda + ' ?');
                  $('#modal_estornar_body').html('');

              });

            }
        });
      }

      function pagination(response){

        $("#pagination").html("");

        var primeira_pagina = "<button id='primeira_pagina' class='btn' style='margin-right:5px;background-image: linear-gradient(to right, #e6774c, #f92278);border-radius: 40px;color:white'>1</button>";

        $("#pagination").append(primeira_pagina);

        if(response.meta.current_page == '1'){
          $("#primeira_pagina").attr('disabled',true);
        }

        $('#primeira_pagina').on("click", function(){
          atualizar('?page=1');
        });

        for(x=3;x>0;x--){

          if(response.meta.current_page - x <= 1){
            continue;
          }

          $("#pagination").append("<button id='pagina_"+( response.meta.current_page - x )+"' class='btn' style='margin-right:5px;background-image: linear-gradient(to right, #e6774c, #f92278);border-radius: 40px;color:white'>"+(response.meta.current_page - x)+"</button>");

          $('#pagina_'+( response.meta.current_page - x )).on("click", function(){
            atualizar('?page='+$(this).html());
          });

        }

        if(response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page){
          var pagina_atual = "<button id='pagina_atual' class='btn btn-primary' style='margin-right:5px;background-image: linear-gradient(to right, #e6774c, #f92278);border-radius: 40px;color:white'>"+(response.meta.current_page)+"</button>";

          $("#pagination").append(pagina_atual);

          $("#pagina_atual").attr('disabled',true);
        }

        for(x=1;x<4;x++){

          if(response.meta.current_page + x >= response.meta.last_page){
            continue;
          }

          $("#pagination").append("<button id='pagina_"+( response.meta.current_page + x )+"' class='btn' style='margin-right:5px;background-image: linear-gradient(to right, #e6774c, #f92278);border-radius: 40px;color:white'>"+(response.meta.current_page + x)+"</button>");

          $('#pagina_'+( response.meta.current_page + x )).on("click", function(){
            atualizar('?page='+$(this).html());
          });

        }

        if(response.meta.last_page != '1'){
          var ultima_pagina = "<button id='ultima_pagina' class='btn' style='background-image: linear-gradient(to right, #e6774c, #f92278);border-radius: 40px;color:white'>"+response.meta.last_page+"</button>";

          $("#pagination").append(ultima_pagina);

          if(response.meta.current_page == response.meta.last_page){
            $("#ultima_pagina").attr('disabled',true);
          }

          $('#ultima_pagina').on("click", function(){
            atualizar('?page='+response.meta.last_page);
          });
        }

      }

    });

  </script>
@endsection

