{{--Adicionar nova entrada--}}
<div class="row">
    <div class="col-lg-12">
        <h4> Adicionar nova entrada </h4>
    </div>
    <div class="col-lg-12">
        <a id='shopify' hidden data-shopify='{{$project->shopify_id ?? null}}'></a>
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
            @if(!$haveEnterA && $project->shopify_id == null)
                <p class='info mt-12' style='font-size: 10px;'>
                    <i class='icon wb-info-circle' aria-hidden='true'></i><strong> Caso você possua um site hospedado em algum servidor com este domínio, você precisa criar uma entrada A com seu domínio apontando para o IP do servidor para seu site continuar funcionando normalmente. </strong>
                </p>
            @endif
        </form>
    </div>
</div>
{{--Adicionar nova Entrada--}}
{{--Lista entrada personalizada--}}
<div class="row mx-2">
    <h4>Entradas personalizadas</h4>
</div>
<div class='col-xl-12 col-lg-12'>
    <div id='divCustomDomain' class="table-responsive overflow:scroll">
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
                                    {{--<td class='col-6' style='overflow-x:scroll'>{{ $register['content'] }}</td>--}}
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

