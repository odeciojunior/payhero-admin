{{--@extends("layouts.master")--}}

{{--@section('content')--}}

  {{--<!-- Page -->--}}
  {{--<div class="page">--}}

    {{--<div class="page-content container-fluid">--}}
      {{--<div class="panel pt-30 p-30" data-plugin="matchHeight">--}}

        {{--<div class="row">--}}
            {{--<div class="col-9">--}}
                {{--<h3 style="margin: 30px 0 20px 0">Histórico de SMS</h3>--}}
            {{--</div>--}}
            {{--<div class="col-3">--}}
                {{--<button class="btn btn-primary" data-toggle='modal' data-target='#modal_enviar_sms_manual'>Enviar SMS manual</button>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<div class="row">--}}

            {{--<div class="col-12">--}}
                {{--<table id="tabela_sms" class="display w-full table-stripped" style="width:100%">--}}
                    {{--<thead>--}}
                        {{--<th>Plano</th>--}}
                        {{--<th>Tipo</th>--}}
                        {{--<th>Número</th>--}}
                        {{--<th>Mensagem</th>--}}
                        {{--<th>Data</th>--}}
                        {{--<th>Evento</th>--}}
                        {{--<th>Status</th>--}}
                    {{--</thead>--}}
                    {{--<tbody>--}}
                    {{--</tbody>--}}
                {{--</table>--}}
            {{--</div>--}}
        {{--</div>--}}

        {{--<div id="modal_enviar_sms_manual" class="modal fade example-modal-lg modal-3d-flip-vertical" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">--}}
            {{--<div class="modal-dialog modal-simple">--}}
                {{--<div class="modal-content">--}}
                    {{--<div class="modal-header">--}}
                        {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                            {{--<span aria-hidden="true">×</span>--}}
                        {{--</button>--}}
                        {{--<h4 class="modal-title" style="width: 100%; text-align:center">Enviar SMS</h4>--}}
                    {{--</div>--}}
                    {{--<div class="modal-body">--}}
                        {{--<form id="form_sms_manual" style="margin-top: 40px">--}}
                            {{--<div class="row">--}}
                                {{--<div class="form-group col-12">--}}
                                    {{--<label for="telefone">Celular</label>--}}
                                    {{--<input name="telefone" class="form-control" id="telefone" placeholder="celular">--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="row">--}}
                                {{--<div class="form-group col-12">--}}
                                    {{--<label for="mensagem">Mensagem</label>--}}
                                    {{--<input name="mensagem" class="form-control" id="mensagem" placeholder="mensagem">--}}
                                {{--</div>--}}
                            {{--</div>    --}}
                        {{--</form>--}}
                    {{--</div>--}}
                    {{--<div class="modal-footer text-center">--}}
                        {{--<button id="enviar_mensagem_manual" type="button" class="btn btn-success" data-dismiss="modal">Enviar</button>--}}
                        {{--<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
      {{--</div>--}}
    {{--</div>--}}
  {{--</div>--}}

  {{--<script>--}}

    {{--$(document).ready( function(){--}}

        {{--$("#telefone").mask("(00) 00000-0000");--}}

        {{--$("#tabela_sms").DataTable( {--}}
            {{--bLengthChange: false,--}}
            {{--ordering: false,--}}
            {{--processing: true,--}}
            {{--serverSide: true,--}}
            {{--headers: {--}}
                {{--'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
            {{--},--}}
            {{--ajax: {--}}
                {{--url: '/sms/dadosmensagens',--}}
                {{--type: 'POST'--}}
            {{--},--}}
            {{--columns: [--}}
                {{--{ data: 'plano', name: 'plano', orderable: "false"},--}}
                {{--{ data: 'tipo', name: 'tipo', orderable: "false"},--}}
                {{--{ data: 'para', name: 'para', orderable: "false"},--}}
                {{--{ data: 'mensagem', name: 'mensagem', orderable: "false"},--}}
                {{--{ data: 'data', name: 'data', orderable: "false"},--}}
                {{--{ data: 'evento', name: 'evento', orderable: "false"},--}}
                {{--{ data: 'status', name: 'status', orderable: "false"},--}}
            {{--],--}}
            {{--"language": {--}}
                {{--"sProcessing":    "Procesando...",--}}
                {{--"lengthMenu": "Apresentando _MENU_ registros por página",--}}
                {{--"zeroRecords": "Nenhum registro encontrado",--}}
                {{--"info": "Apresentando página _PAGE_ de _PAGES_",--}}
                {{--"infoEmpty": "Nenhum registro encontrado",--}}
                {{--"infoFiltered": "(filtrado por _MAX_ registros)",--}}
                {{--"sInfoPostFix":   "",--}}
                {{--"sSearch":        "Procurar :",--}}
                {{--"sUrl":           "",--}}
                {{--"sInfoThousands":  ",",--}}
                {{--"sLoadingRecords": "Carregando...",--}}
                {{--"oPaginate": {--}}
                    {{--"sFirst":    "Primeiro",--}}
                    {{--"sLast":    "Último",--}}
                    {{--"sNext":    "Próximo",--}}
                    {{--"sPrevious": "Anterior",--}}
                {{--},--}}
            {{--},--}}
        {{--});--}}

        {{--$("#enviar_mensagem_manual").on("click", function(){--}}

            {{--var form_data = new FormData(document.getElementById('form_sms_manual'));--}}

            {{--$.ajax({--}}
                {{--method: "POST",--}}
                {{--url: "/sms/enviarsmsmanual",--}}
                {{--processData: false,--}}
                {{--contentType: false,--}}
                {{--cache: false,--}}
                {{--data: form_data,--}}
                {{--headers: {--}}
                    {{--'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
                {{--},--}}
                {{--error: function(){--}}
                    {{--//--}}
                {{--},--}}
                {{--success: function(data){--}}

                    {{--if(data == 'Sucesso'){--}}
                        {{--swal({--}}
                            {{--position: 'bottom',--}}
                            {{--type: 'success',--}}
                            {{--toast: 'true',--}}
                            {{--title: 'Mensagem enviada',--}}
                            {{--showConfirmButton: false,--}}
                            {{--timer: 6000--}}
                        {{--});                --}}
                    {{--}--}}
                    {{--else{--}}
                        {{--swal({--}}
                            {{--position: 'bottom',--}}
                            {{--type: 'error',--}}
                            {{--toast: 'true',--}}
                            {{--title: data,--}}
                            {{--showConfirmButton: false,--}}
                            {{--timer: 6000--}}
                        {{--});                --}}
                    {{--}--}}

                    {{--$("#tabela_sms").DataTable().ajax.reload();--}}

                {{--},--}}
            {{--});--}}

        {{--});--}}
    {{--});--}}

  {{--</script>--}}


{{--@endsection--}}

