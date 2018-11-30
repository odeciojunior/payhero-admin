<div style="text-align: center">
    <h4> Editar domínio </h4>
</div>

<form id="editar_dominio" method="post" action="/dominios/editardominio">
    @csrf
    <input type="hidden" value="{!! $dominio->id !!}" name="id">
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">

                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="dominio">Domínio</label>
                        <input value="{!! $dominio->dominio != '' ? $dominio->dominio : '' !!}" name="dominio" type="text" class="form-control" id="dominio" placeholder="Domínio">
                    </div>
                </div>

                <div class="row">

                    <div class="form-group col-xl-6">
                        <label for="layout">Layout</label>
                        <select name="layout" type="text" class="form-control" id="layout" required>
                            @foreach($layouts as $layout)
                                <option value="{{ $layout['id'] }}" {!! ($dominio->layout == $layout['id']) ? 'selected' : '' !!}>{{ $layout['descricao'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-xl-6">
                        <label for="empresa">Empresa</label>
                        <select name="empresa" type="text" class="form-control" id="empresa" required>
                            <option value="" selected>Selecione</option>
                            @foreach($empresas as $empresa)
                                <option value="{{ $empresa['id'] }}" {!! ($dominio->empresa == $empresa['id']) ? 'selected' : '' !!}>{{ $empresa['nome_fantasia'] }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

            </div>

        </div>
    </div>
</form>
