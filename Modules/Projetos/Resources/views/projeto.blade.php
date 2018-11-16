@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

    <div class="page-header">
        <h1 class="page-title">Projeto {{ $projeto->nome }}</h1>
        <div class="page-header-actions">
            <a class="btn btn-primary float-right" href="/projetos">
                Voltar
            </a>
        </div>
    </div>

    <div class="page-content container-fluid">
      <div class="panel pt-10 p-10" data-plugin="matchHeight">

        <div class="col-xl-12">
            <div class="example-wrap">
              <div class="nav-tabs-horizontal" data-plugin="tabs">
                <ul class="nav nav-tabs" role="tablist">
                  <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#tab_info_geral"
                      aria-controls="tab_info_geral" role="tab">Informações gerais</a></li>
                  <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_produtos"
                      aria-controls="tab_produtos" role="tab">Produtos</a></li>
                  <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_planos"
                      aria-controls="tab_planos" role="tab">Planos</a></li>
                  <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_pixels"
                      aria-controls="tab_pixels" role="tab">Pixels</a></li>
                  <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_brindes"
                      aria-controls="tab_brindes" role="tab">Brindes</a></li>
                  <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_cupons"
                      aria-controls="tab_cupons" role="tab">Cupons de desconto</a></li>
                </ul>
                <div class="tab-content pt-20">
                  <div class="tab-pane active" id="tab_info_geral" role="tabpanel">
                    <div class="col-md-10">
                        <table class="table table-bordered table-hover table-striped">
                            <tbody>
                                <tr>
                                    <td><b>Nome</b></td>
                                    <td>{{ $projeto->nome }}</td>
                                </tr>
                                <tr>
                                    <td><b>Descrição</b></td>
                                    <td>{{ $projeto->descricao }}</td>
                                </tr>
                                <tr>
                                    <td><b>Visibilidade</b></td>
                                    <td>{{ ($projeto->visibilidade == 'publico') ? 'Projeto público' : 'Projeto privado' }}</td>
                                </tr>
                                <tr>
                                    <td><b>Status</b></td>
                                    <td>{{ $projeto->status ? 'Ativo' : 'Inativo' }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <img src="{{ $foto }}" alt="Imagem não encontrada" style="height: 200px; width: 200px"/>
                    </div>
                </div>
                <div class="tab-pane" id="tab_produtos" role="tabpanel">
                    <table id="tabela_produtos" class="table-bordered table-hover w-full" style="margin-top: 20px">
                        <thead class="bg-blue-grey-100">
                            <tr>
                                <td>Nome</td>
                                <td>Descrição</td>
                                <td>Categoria</td>
                                <td>Formato</td>
                                <td>Quantidade</td>
                                <td>Status</td>
                                <td style="min-width: 160px;max-width:160px">Detalhes</td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="tab_planos" role="tabpanel">
                    <table id="tabela_planos" class="table-bordered table-hover w-full" style="margin-top: 80px">
                        <thead class="bg-blue-grey-100">
                            <tr>
                                <td>Nome</td>
                                <td>Descrição</td>
                                <td>Código</td>
                                <td>Preço</td>
                                <td style="min-width: 160px;max-width:160px">Detalhes</td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="tab_pixels" role="tabpanel">
                    <table id="tabela_pixels" class="table-bordered table-hover w-full" style="margin-top: 80px">
                        <thead class="bg-blue-grey-100">
                            <tr>
                                <td>Nome</td>
                                <td>Código</td>
                                <td>Plataforma</td>
                                <td>Status</td>
                                <td style="min-width: 160px;max-width:160px">Detalhes</td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                  <div class="tab-pane" id="tab_brindes" role="tabpanel">
                    Norafe zumeu texit consilio fugiendam, opinionum levius amici inertissimae pecuniae
                    tribus ordiamur, alienus artes solitudo, minime praesidia
                    proficiscuntur reiciat detracta involuta veterum. Rutilius
                    quis honestatis hominum, quisquis percussit sibi explicari.
                  </div>
                  <div class="tab-pane" id="tab_cupons" role="tabpanel">
                    Alacat manus texit consilio fugiendam, opinionum levius amici inertissimae pecuniae
                    tribus ordiamur, alienus artes solitudo, minime praesidia
                    proficiscuntur reiciat detracta involuta veterum. Rutilius
                    quis honestatis hominum, quisquis percussit sibi explicari.
                  </div>
                </div>
              </div>
            </div>
            <!-- End Example Tabs -->
          </div>
        </div>
    </div>
  </div>

  <script>

    $(document).ready( function(){

        $("#tabela_produtos").DataTable( {
            bLengthChange: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: '/produtos/data-source',
                type: 'POST'
            },
            columns: [
                { data: 'nome', name: 'nome'},
                { data: function(data){
                    return data.descricao.substr(0,25); 
                }, name: 'descricao'},
                { data: 'categoria_nome', name: 'categoria_nome'},
                { data: function(data){
                    if(data.formato == 1)
                      return 'Físico';
                    if(data.formato == 0)
                      return 'Digital';
                    return 'null';
                }, name: 'formato'},
                { data: 'quntidade', name: 'quntidade'},
                { data: function(data){
                  if(data.disponivel == 1)
                  return 'Disponível';
                if(data.disponivel == 0)
                  return 'Indisponível';
                return 'null';
                }, name: 'disponivel'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false },
            ],
            "language": {
                "sProcessing":    "Procesando...",
                "lengthMenu": "Apresentando _MENU_ registros por página",
                "zeroRecords": "Nenhum registro encontrado no banco de dados",
                "info": "Apresentando página _PAGE_ de _PAGES_",
                "infoEmpty": "Nenhum registro encontrado no banco de dados",
                "infoFiltered": "(filtrado por _MAX_ registros)",
                "sInfoPostFix":   "",
                "sSearch":        "Procurar :",
                "sUrl":           "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Carregando...",
                "oPaginate": {
                    "sFirst":    "Primeiro",
                    "sLast":    "Último",
                    "sNext":    "Próximo",
                    "sPrevious": "Anterior",
                },
            },
            "drawCallback": function() {

            }

        });



        $("#tabela_planos").DataTable( {
            processing: true,
            serverSide: true,
            ajax: {
                url: '/planos/data-source',
                type: 'POST'
            },
            columns: [
                { data: 'nome', name: 'nome'},
                { data: function(data){
                    if(data.descricao == null)
                        return '';
                    else
                        return data.descricao.substr(0,25);   
                }, name: 'descricao'},
                { data: 'cod_identificador', name: 'cod_identificador'},
                { data: 'preco', name: 'preco'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false },
            ],
            "language": {
                "sProcessing":    "Procesando...",
                "lengthMenu": "Apresentando _MENU_ registros por página",
                "zeroRecords": "Nenhum registro encontrado no banco de dados",
                "info": "Apresentando página _PAGE_ de _PAGES_",
                "infoEmpty": "Nenhum registro encontrado no banco de dados",
                "infoFiltered": "(filtrado por _MAX_ registros)",
                "sInfoPostFix":   "",
                "sSearch":        "Procurar :",
                "sUrl":           "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Carregando...",
                "oPaginate": {
                    "sFirst":    "Primeiro",
                    "sLast":    "Último",
                    "sNext":    "Próximo",
                    "sPrevious": "Anterior",
                },
            },
            "drawCallback": function() {

            }

        });


        $("#tabela_pixels").DataTable( {

            processing: true,
            serverSide: true,
            ajax: {
                url: '/pixels/data-source',
                type: 'POST'
            },
            columns: [
                { data: 'nome', name: 'nome'},
                { data: 'cod_pixel', name: 'cod_pixel'},
                { data: 'plataforma', name: 'plataforma'},
                { data: function(data){
                    if(data.status == 1){
                      return 'Ativo';
                    }
                    else{
                      return 'Inativo';
                    }
                }, name: 'status'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false },
            ],
            "language": {
                "sProcessing":    "Procesando...",
                "lengthMenu": "Apresentando _MENU_ registros por página",
                "zeroRecords": "Nenhum registro encontrado no banco de dados",
                "info": "Apresentando página _PAGE_ de _PAGES_",
                "infoEmpty": "Nenhum registro encontrado no banco de dados",
                "infoFiltered": "(filtrado por _MAX_ registros)",
                "sInfoPostFix":   "",
                "sSearch":        "Procurar :",
                "sUrl":           "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Carregando...",
                "oPaginate": {
                    "sFirst":    "Primeiro",
                    "sLast":    "Último",
                    "sNext":    "Próximo",
                    "sPrevious": "Anterior",
                },
            },
            "drawCallback": function() {

            }

        });

    });

  </script>


@endsection

