@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">
    <div class="page-content container-fluid">

      <h3> Vendas </h3>

      <div class="panel pt-30 p-30 " data-plugin="matchHeight" style="min-height: 300px">

        <table id="tabela_vendas" class="table-hover table-striped table-bordered" style="width:100%;padding:0;margin:0">
          <thead>
            <th>Transação</th>
            <th>Projeto</th>
            <th>Descrição</th>
            <th>Comprador</th>
            <th>Forma</th>
            <th>Status</th>
            <th>Data</th>
            <th>Pagamento</th>
            <th>Valor total</th>
            <th>Valor líquido</th>
            <th>Detalhes</th>
          </thead>
          <tbody id="dados_tabela">

          </tbody>
        </table>
        <ul id="pagination" class="pagination-sm" style="margin-top:10px;position:relative;float:right"></ul>

        <!-- Modal detalhes da venda-->
        <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_detalhes" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
          <div class="modal-dialog modal-simple">
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

      function atualizar(link = null){

        $('#dados_tabela').html("<tr class='text-center'><td colspan='11'> Carregando...</td></tr>");

        if(link == null){
          link = '/relatorios/getvendas';
        }
        else{
          link = '/relatorios/getvendas'+link;
        }

        $.ajax({
            method: "GET",
            url: link,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function(){
                alert('Ocorreu algum erro');
            },
            success: function(response){
              $('#dados_tabela').html('');

              $.each(response.data, function(index, value){
                dados = '';
                dados += '<tr>';
                dados += "<td>"+value.id+"</td>";
                dados += "<td>"+value.projeto+"</td>";
                dados += "<td>"+value.produto+"</td>";
                dados += "<td>"+value.comprador+"</td>";

                if(value.forma == 'Boleto'){
                  dados += "<td><img src='/assets/img/boleto.jpeg' style='width: 60px'></td>";
                }else{
                  dados += "<td><img src='/assets/img/cartao.jpg' style='width: 60px'></td>";
                }

                if(value.status == 'paid'){
                  dados += "<td><span class='badge badge-success'>Aprovada</span></td>";
                } else if(value.status == 'refused'){
                  dados += "<td><span class='badge badge-danger'>Recusada</span></td>";
                }else if(value.status == 'chargedback' || value.status == 'refunded'){
                  dados += "<td><span class='badge badge-secondary'>Estornada</span></td>";
                }else if(value.status == 'waiting_payment'){
                  dados += "<td><span class='badge badge-primary'>Aguardando pagamento</span></td>";
                }else{
                  dados += "<td><span class='badge badge-primary'>"+value.status+"</span></td>";
                }

                dados += "<td>"+value.data_inicio+"</td>";
                dados += "<td>"+value.data_finalizada+"</td>";
                dados += "<td>"+value.total_pago+"</td>";
                dados += "<td>"+value.total_pago+"</td>";
                dados += "<td><button class='btn btn-sm btn-outline btn-primary detalhes_venda' venda='"+value.id+"' data-target='#modal_detalhes' data-toggle='modal' type='button'>Detalhes</button></td>";
                dados += '</tr>';
                $("#dados_tabela").append(dados);

              });

              pagination(response);

              var id_venda = '';

              $('.detalhes_venda').unbind('click');

              $('.detalhes_venda').on('click', function() {

                  var venda = $(this).attr('venda');

                  $('#modal_venda_titulo').html('Detalhes da venda ' + venda);

                  $('#modal_detalhes_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");

                  var data = { id_venda : venda };

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

        var primeira_pagina = "<button id='primeira_pagina' class='btn btn-default' style='margin-right:5px'>1</button>";

        $("#pagination").append(primeira_pagina);

        if(response.meta.current_page == '1'){
          $("#primeira_pagina").attr('disabled',true);
          $("#primeira_pagina").removeClass('btn-default');
          $("#primeira_pagina").addClass('btn-primary');
        }

        $('#primeira_pagina').on("click", function(){
          atualizar('?page=1');
        });

        for(x=3;x>0;x--){

          if(response.meta.current_page - x <= 1){
            continue;
          }

          $("#pagination").append("<button id='pagina_"+( response.meta.current_page - x )+"' class='btn btn-default' style='margin-right:5px'>"+(response.meta.current_page - x)+"</button>");

          $('#pagina_'+( response.meta.current_page - x )).on("click", function(){
            atualizar('?page='+$(this).html());
          });

        }

        if(response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page){
          var pagina_atual = "<button id='pagina_atual' class='btn btn-primary' style='margin-right:5px'>"+(response.meta.current_page)+"</button>";

          $("#pagination").append(pagina_atual);

          $("#pagina_atual").attr('disabled',true);
        }

        for(x=1;x<4;x++){

          if(response.meta.current_page + x >= response.meta.last_page){
            continue;
          }

          $("#pagination").append("<button id='pagina_"+( response.meta.current_page + x )+"' class='btn btn-default' style='margin-right:5px'>"+(response.meta.current_page + x)+"</button>");

          $('#pagina_'+( response.meta.current_page + x )).on("click", function(){
            atualizar('?page='+$(this).html());
          });

        }

        var ultima_pagina = "<button id='ultima_pagina' class='btn btn-default'>"+response.meta.last_page+"</button>";

        $("#pagination").append(ultima_pagina);

        if(response.meta.current_page == response.meta.last_page){
          $("#ultima_pagina").attr('disabled',true);
          $("#ultima_pagina").addClass('btn-primary');
        }

        $('#ultima_pagina').on("click", function(){
          atualizar('?page='+response.meta.last_page);
        });


      }

});

  </script>
@endsection

