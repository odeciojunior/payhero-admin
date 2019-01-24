<form id="atualizar_configuracoes" method="post" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" value="{!! $projeto->id !!}">
    <div style="width:100%">
        <div class="row">
            <div class="form-group col-xl-12">
                <label for="nome">Nome</label>
                <input name="nome" value="{!! $projeto->nome !!}" type="text" class="form-control" id="nome" placeholder="Nome do projeto" required>
            </div>
            {{--  <div class="form-group col-xl-6">
                <label for="emrpesa">Empresa</label>
                <select name="empresa" class="form-control" id="empresa" required>
                    <option value="">Selecione</option>
                    @foreach($empresas as $empresa)
                        <option value="{!! $empresa->id !!}" {!! ($empresa->id == $projeto->empresa) ? 'selected' : '' !!}>{!! $empresa->nome_fantasia !!}</option>
                    @endforeach
                </select>
            </div>  --}}
        </div>

        <div class="row">
            <div class="form-group col-xl-12">
                <label for="descricao">Descrição</label>
                <input name="descricao" value="{!! $projeto->descricao !!}" type="text" class="form-control" id="descricao" placeholder="Descrição">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-xl-6">
                <label for="visibilidade">Visibilidade</label>
                <select name="visibilidade" class="form-control" id="visibilidade" required>
                    <option value="">Selecione</option>
                    <option value="publico" {!! $projeto->visibilidade == 'publico' ? 'selected' : '' !!}>Projeto público</option>
                    <option value="privado" {!! $projeto->visibilidade == 'privado' ? 'selected' : '' !!}>Projeto privado</option>
                </select>
            </div>

            <div class="form-group col-xl-6">
                <label for="status">Status</label>
                <select name="status" class="form-control" id="status" required>
                    <option value="">Selecione</option>
                    <option value="1" {!! $projeto->status == '1' ? 'selected' : '' !!}>Ativo</option>
                    <option value="0" {!! $projeto->status == '0' ? 'selected' : '' !!}>Inativo</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-xl-6">
                <label for="porcentagem_afiliados">Porcentagem para afiliados</label>
                <input name="porcentagem_afiliados" value="{!! $projeto->porcentagem_afiliados !!}" type="text" class="form-control" id="porcentagem_afiliados" placeholder="Porcentagem">
            </div>

            <div class="form-group col-xl-6">
                <label for="descricao_fatura">Descrição na fatura</label>
                <input name="descricao_fatura" value="{!! $projeto->descricao_fatura !!}" type="text" class="form-control" id="descricao_fatura" placeholder="Descrição do projeto na fatura">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-12">
                <label for="selecionar_foto">Imagem do projeto</label><br>
                <input type="button" id="selecionar_foto" class="btn btn-default" value="Alterar foto do projeto">
                <input name="foto_projeto" type="file" class="form-control" id="foto_projeto" style="display:none" accept="image/*">
                <div  style="margin: 20px 0 0 30px;">
                    <img id="previewimage" alt="Selecione a foto do projeto" style="max-height: 250px; max-width: 350px;" src="{!! url(\Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO.$projeto->foto)!!}"/>
                </div>
                <input type="hidden" name="foto_x1"/>
                <input type="hidden" name="foto_y1"/>
                <input type="hidden" name="foto_w"/>
                <input type="hidden" name="foto_h"/>
            </div>
        </div>

        <div class="row" style="margin-top: 30px">
            <div class="form-group">
                <button id="bt_atualizar_configuracoes" type="button" class="btn btn-success">Atualizar dados do projeto</button>
            </div>
        </div>

    </div>
</form>

