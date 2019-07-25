{{--Adicionar nova entrada--}}
<div class="row">
    <div class="col-lg-12">
        <h4> Adicionar nova entrada </h4>
    </div>
    <div class="col-lg-12">
        <form id="form-edit-domain" class="" method="post" action="{{route('domain.update', ['id' => $domain->id_code])}}">
            @csrf
            <div class='row'>
                <div class="form-group mb-2 col-md-2">
                    <select id="tipo_registro" class="form-control input-pad">
                        <option value="A">A</option>
                        <option value="AAA">AAA</option>
                        <option value="CNAME">CNAME</option>
                        <option value="TXT">TXT</option>
                        <option value="MX">MX</option>
                    </select>
                </div>
                <div class="form-group mx-sm-3 mb-3 col-md-4">
                    <input id="nome_registro" class="input-pad" placeholder="Nome">
                </div>
                <div class="form-group mx-sm-3 mb-3 col-md-4">
                    <input id="valor_registro" class="input-pad" placeholder="Valor">
                </div>
                <div class="form-group mx-sm-3 mb-3 col-md-1">
                    <button class='btn btn-primary' id='bt_add_record'>Adicionar</button>
                </div>
            </div>
        </form>
    </div>
</div>
{{--Adicionar nova Entrada--}}
{{--Lista entrada personalizada--}}
<div class="row mx-2">
    <h4>Entradas personalizadas</h4>
</div>
<div class='col-xl-12 col-lg-12'>
    <div class="table-responsive overflow:scroll">
        @if(isset($registers))
            @if(count($registers) > 0)
                <table id='new_registers_table' class="table table-hover table-bordered table-stripped" style='table-layout: fixed;'>
                    <thead>
                        <tr>
                            <th class='col-2'>Tipo</th>
                            <th class='col-2'>Nome</th>
                            <th class='col-6'>Conteúdo</th>
                            <th class='col-2'></th>
                        </tr>
                    </thead>
                    <tbody id="new_registers">
                        @foreach($registers as $register)
                            @if($register['system_flag'] == 0)
                                <tr data-save='1'>
                                    <td class='col-2'>{{ $register['type']}}</td>
                                    <td class='col-2'>{{ $register['name'] }}</td>
                                    {{--                                <td class='col-6' style='overflow-x:scroll'>{{ $register['content'] }}</td>--}}
                                    <td class='col-6'>{{ $register['content'] }}</td>
                                    <td class='col-2 text-center align-middle'>
                                        <button type="button" id-registro="{!! $register['id'] !!}" class="btn btn-danger remover_registro" {!! ($register['system_flag'] == 1) ? 'disabled' : '' !!}>Remover</button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class='alert alert-info' align='center'>
                    <h4>Você ainda não possui nenhuma entrada personalizada!</h4>
                    <h4>Fique a vontade para adicionar acima!</h4>
                </div>
            @endif
        @endif
        <div class='bg-info'>
        </div>
    </div>
</div>
{{--Lista entrada personalizada--}}
{{--<div class="row">
    <div class="form-group col-6">
        <label for="dominio">Domínio</label>
        <input value="{!! $domain->name != '' ? $domain->name : '' !!}" type="text" class="input-pad" id="dominio" placeholder="Domínio" disabled>
    </div>
    <div class="form-group col-6">
        <label for="ip_dominio">IP que o domínio aponta</label>
        @if($project['shopify_id'] != '')
            <input value="IP do Shopify" type="text" class="input-pad" disabled>
        @else
            <input value="{!! $domain->domain_ip != '' ? $domain->domain_ip : '' !!}" name="ip_dominio" type="text" class="input-pad" id="ip_dominio_editar" placeholder="Ip do domínio">
        @endif
    </div>
</div>--}}
{{--@if(isset($registers))

    <div class="row mx-2">
        <h4>Entradas do sistema</h4>
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
                <tbody id="registers">
                    @foreach($registers as $register)
                        @if($register['system_flag'] == 1)
                            <tr>
                                <td class='col-2'>{{ $register['type']}}</td>
                                <td class='col-2'>{{ $register['name'] }}</td>
                                --}}{{--                                <td class='col-6' style='overflow-x:scroll'>{{ $register['content'] }}</td>--}}{{--
                                <td class='col-6'>{{ $register['content'] }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="row text-center" style="padding: 20px">
                <h5>erro na conexão com domínio</h5>
            </div>
        @endif
    </div>--}}
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
