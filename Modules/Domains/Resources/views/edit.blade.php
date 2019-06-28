<form id="editar_dominio" method="post" action="/dominios/editardominio">
    @csrf
    <input type="hidden" value="{{Hashids::encode($domain->id)}}" name="id" id="id_dominio">
    <div class="container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div class="row">
                <div class="form-group col-6">
                    <label for="dominio">Domínio</label>
                    <input value="{!! $domain->name != '' ? $domain->name : '' !!}" type="text" class="form-control" id="dominio" placeholder="Domínio" disabled>
                </div>
                <div class="form-group col-6">
                    <label for="ip_dominio">Ip que o domínio aponta</label>
                    @if($project['shopify_id'] != '')
                        <input value="Ip do shopify" type="text" class="form-control" disabled>
                    @else
                        <input value="{!! $domain->domain_ip != '' ? $domain->domain_ip : '' !!}" name="ip_dominio" type="text" class="form-control" id="ip_dominio_editar" placeholder="Ip do domínio">
                    @endif
                </div>
            </div>
            @if(isset($registers))

                <div class="row mx-2">
                    <h4>Entradas</h4>
                </div>
                <div class='col-xl-12 col-lg-12'>
                    <div class="table-responsive overflow:scroll">
                        <table class="table table-hover table-bordered table-stripped" style='table-layout: fixed;'>
                            <thead>
                                <tr>
                                    <th class='col-2'>Tipo</th>
                                    <th class='col-2'>Nome</th>
                                    <th class='col-6'>Conteúdo</th>
                                    <th class='col-2'></th>
                                </tr>
                            </thead>
                            <tbody id="registros">
                                @foreach($registers as $register)
                                    <tr>
                                        <td class='col-2'>{{ $register['tipo']}}</td>
                                        <td class='col-2'>{{ $register['nome'] }}</td>
                                        <td class='col-6' style='overflow-x:scroll'>{{ $register['valor'] }}</td>
                                        <td class='col-2 text-center align-middle'>
                                            <button type="button" id-registro="{!! $register['id'] !!}" class="btn btn-danger remover_registro" disabled {!! !$register['deletar'] ? 'disabled' : '' !!}>Remover</button>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr id="novos_registros">
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div><h5>Adicionar nova entrada</h5></div>
                                    </td>
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
                                    <td>
                                        <button id="bt_adicionar_entrada" type="button" class="btn btn-primary">Adicionar</button>
                                    </td>
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
                {{--<div class='col-xl-12 col-lg-12'>--}}
                {{--<table class='table table-bordered table-hover table-striped'>--}}
                {{--<thead>--}}
                {{--<tr>--}}
                {{--<th>Tipo</th>--}}
                {{--<th>Nome</th>--}}
                {{--<th>Conteúdo</th>--}}
                {{--<th></th>--}}
                {{--</tr>--}}
                {{--</thead>--}}
                {{--<tbody>--}}
                {{--<tr>--}}
                {{--<td><b>Domínio:</b></td>--}}
                {{--<td>dasdsa</td>--}}
                {{--<td>dasdasd</td>--}}
                {{--<td>dasdasd</td>--}}
                {{--</tr>--}}
                {{--<tr>--}}
                {{--<td><b>IP que o domínio aponta:</b></td>--}}
                {{--<td>dasdasd</td>--}}
                {{--</tr>--}}
                {{--@foreach ($zones as $zone)--}}
                {{--@if($zone->name == $domain->name)--}}
                {{--@foreach ($zone->name_servers as $new_name_server)--}}
                {{--<tr>--}}
                {{--<td><b>Novo servidor DNS :</b></td>--}}
                {{--<td> {!! $new_name_server !!}</td>--}}
                {{--</tr>--}}
                {{--@endforeach--}}
                {{--@endif--}}
                {{--@endforeach--}}
                {{--</tbody>--}}
                {{--</table>--}}
                {{--</div>--}}
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
