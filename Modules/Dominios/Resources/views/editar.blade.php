<div style="text-align: center">
    <h4> Editar domínio </h4>
</div>

<form id="editar_dominio" method="post" action="/dominios/editardominio">
    @csrf
    <input type="hidden" value="{!! $dominio->id !!}" name="id" id="id_dominio">
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight" style="padding:30px">
            <div style="width:100%">

                <div class="row">
                    <div class="form-group col-6">
                        <label for="dominio">Domínio</label>
                        <input value="{!! $dominio->dominio != '' ? $dominio->dominio : '' !!}" type="text" class="form-control" id="dominio" placeholder="Domínio" disabled>
                    </div>

                    <div class="form-group col-6">
                        <label for="ip_dominio">Ip que o domínio aponta</label>
                        <input value="{!! $dominio->ip_dominio != '' ? $dominio->ip_dominio : '' !!}" name="ip_dominio" type="text" class="form-control" id="ip_dominio_editar" placeholder="Ip do domínio">
                    </div>
                </div>

            </div>

            @if(isset($registros))
            
                <div class="row" style="margin-top: 30px">
                    <h4>Entradas</h4>
                </div>

                <div class="row" style="margin-top: 30px">

                    <table class="table table-hover table-bordered table-stripped" style="padding:20px">
                        <thead>
                            <th>Tipo</th>
                            <th>Nome</th>
                            <th>Conteúdo</th>
                            <th></th>
                        </thead>
                        <tbody id="registros">
                            @foreach($registros as $registro)
                                <tr>
                                    <td>{!! $registro['tipo'] !!}</td>
                                    <td>{!! $registro['nome'] !!}</td>
                                    <td>{!! $registro['valor'] !!}</td>
                                    <td><button type="button" id-registro="{!! $registro['id'] !!}" class="btn btn-danger remover_registro" {!! !$registro['deletar'] ? 'disabled' : '' !!}>Remover</button></td>
                                </tr>
                            @endforeach
                            <tr id="novos_registros">
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan="4"><div class="text-center"><h5>Adicionar nova entrada</h5></div></td>
                            </tr>
                            <tr>
                                <td>
                                    <select id="tipo_registro" class="form-control">
                                        <option value="A">A</option>
                                        <option value="AAA">AAA</option>
                                        <option value="CNAME">CNAME</option>
                                        <option value="TXT">TXT</option>
                                        <option value="MX">MX</option>
                                    </select>
                                </td>
                                <td><input id="nome_registro" class="form-control" placeholder="Nome"></td>
                                <td><input id="valor_registro" class="form-control" placeholder="Valor"></td>
                                <td><button id="bt_adicionar_entrada" type="button" class="btn btn-primary">Adicionar</button></td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>

            @else
                <div class="row text-center" style="padding: 20px">
                    <h5>erro na conexão com domínio</h5>
                </div>
            @endif

        </div>
    </div>
</form>

<script>

    {{--  $(document).ready(function(){

        var qtd_novos_registros = 1;

        $("#bt_adicionar_entrada").on("click", function(){

            $("#novos_registros").after("<tr registro='"+qtd_novos_registros+"'><td>"+$("#tipo_registro").val()+"</td><td>"+$("#nome_registro").val()+"</td><td>"+$("#valor_registro").val()+"</td><td><button type='button' class='btn btn-danger remover_entrada'>Remover</button></td></tr>");

            $('#editar_dominio').append('<input type="hidden" name="tipo_registro_'+qtd_novos_registros+'" id="tipo_registro_'+qtd_novos_registros+'" value="'+$("#tipo_registro").val()+'" />');
            $('#editar_dominio').append('<input type="hidden" name="nome_registro_'+qtd_novos_registros+'" id="nome_registro_'+qtd_novos_registros+'" value="'+$("#nome_registro").val()+'" />');
            $('#editar_dominio').append('<input type="hidden" name="valor_registro_'+qtd_novos_registros+'" id="valor_registro_'+( qtd_novos_registros++) +'" value="'+$("#valor_registro").val()+'" />');

            $(".remover_entrada").unbind("click");

            $(".remover_entrada").on("click", function(){

                var novo_registro = $(this).parent().parent();
                var id_registro = novo_registro.attr('registro');
                novo_registro.remove();
                alert(id_registro);
                $("#tipo_registro_"+id_registro).remove();
                $("#nome_registro_"+id_registro).remove();
                $("#valor_registro_"+id_registro).remove();
            });

            $("#tipo_registro").val("A");
            $("#nome_registro").val("");
            $("#valor_registro").val("");
        });

        $(".remover_registro").on("click", function(){

            var id_registro = $(this).attr('id-registro');

            var row = $(this).parent().parent();

            $.ajax({
                method: "POST",
                url: "/dominios/removerregistrodns",
                data: {
                    id_registro: id_registro, 
                    id_dominio: $("#id_dominio").val()
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(){
                    alert('Ocorreu algum erro');
                },
                success: function(data){
                    if(data == 'sucesso'){
                        row.remove();
                        alert('Registro removido!');
                    }
                    else{
                        alert(data);
                    }
                },
            });

        });

    });  --}}
</script>
