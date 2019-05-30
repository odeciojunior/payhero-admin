@extends("layouts.master")

@section('content')
<style>
  tbody tr:hover {
    transform: scale(1.03);
  }
  </style>
  <!-- Page -->
  <div class="page">
    <div class="page-content container-fluid">

      <div class="row">
        <div class="col-10">
          <h3> Vendas </h3>
        </div>
        <div class="col-2">
          <button class="btn btn-success" id="filtros"><i class="icon wb-search" aria-hidden="true"></i> Filtros</button>
        </div>
      </div>
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
              <option value="Cartão de crédito">Cartão de crédito</option>
              <option value="Boleto">Boleto</option>
            </select>
          </div>
          <div class="col-3">
            <label for="status">Status</label>
            <select id="status" class="form-control">
              <option value="">Todos status</option>
              <option value="paid">Aprovado</option>
              <option value="waiting_payment">Aguardando pagamento</option>
              <option value="refused">Recusada</option>
              <option value="chargedback">Estornada</option>
            </select>
          </div>
          <div class="col-3">
            <label for="comprador">Comprador</label>
            <input id="comprador" class="form-control" placeholder="comprador">
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

          <table id="tabela_vendas" class="table table-hover text-right" style="width:100%;">
            <thead style="text-align:center" style="margin-bottom:8px">
              <tr>
                <th style='vertical-align: middle'>Transação</th>
                <th style='vertical-align: middle'>Projeto</th>
                <th style='vertical-align: middle'>Descrição</th>
                <th style='vertical-align: middle'>Comprador</th>
                <th style='vertical-align: middle'>Forma</th>
                <th style='vertical-align: middle'>Status</th>
                <th style='vertical-align: middle'>Data</th>
                <th style='vertical-align: middle'>Pagamento</th>
                <th style='vertical-align: middle'>Comissão</th>
                <th style='vertical-align: middle'>Detalhes</th>
              </tr>
            </thead>
            <tbody id="dados_tabela">

            </tbody>
          </table>
          <ul id="pagination" class="pagination-sm" style="margin-top:10px;position:relative;float:right"></ul>
        </div>

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
                  dados += "<td style='vertical-align: middle' class='text-center'><img src='/assets/img/boleto.jpeg' style='width: 60px'></td>";
                }else{
                  if(value.brand == 'mastercard'){
                    dados += "<td style='vertical-align: middle' class='text-center'><img src='/assets/img/master.1.svg' style='width: 60px'></td>";
                  }
                  else if(value.brand == 'visa'){
                    dados += "<td style='vertical-align: middle' class='text-center'><img src='/assets/img/visa.svg' style='width: 60px'></td>";
                  }
                  else{
                    dados += "<td style='vertical-align: middle' class='text-center'><img src='/assets/img/cartao.jpg' style='width: 60px'></td>";
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

