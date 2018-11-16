@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Cadastrar novo layout</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="{{ route('layouts') }}">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-xxl-6 col-lg-6">
                <div class="card card-shadow">
                    <form method="post" action="/layouts/cadastrarlayout">
                        @csrf
                        <div class="page-content container-fluid">
                            <div class="panel" data-plugin="matchHeight">
                                <div style="width:100%">
                                    <div class="row">
                                        <div class="form-group col-xl-12">
                                            <label for="descricao">Descrição</label>
                                            <input name="descricao" type="text" class="form-control" id="descricao" placeholder="Descrição" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xl-12">
                                            <label for="logo">Logo</label>
                                            <input name="logo" type="file" class="form-control" id="logo" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xl-12">
                                            <label for="estilo">Estilo</label>
                                            <select name="estilo" class="form-control" id="estilo" required>
                                                <option value="">Selecione</option>
                                                <option value="Padrao">Padrão</option>
                                                <option value="Backgound Multi Camada">Background multi-camadas</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xl-12">
                                            <label for="cor1">Cor 1</label><br>
                                            <input id="cor1" name="cor1" type="text" style="width: 100%" class="asColorpicker form-control colorInputUi-input" data-plugin="asColorPicker" data-mode="simple" value="#ff666b">
                                            <a href="#" class="colorInputUi-clear">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xl-12">
                                            <label for="cor2">Cor 2</label><br>
                                            <input id="cor2" name="cor2" type="text" style="width: 100%" class="asColorpicker form-control colorInputUi-input" data-plugin="asColorPicker" data-mode="simple" value="#ff666b">
                                            <a href="#" class="colorInputUi-clear">
                                            </a>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-xl-12">
                                            <label for="botao">Botões</label>
                                            <select name="botao" class="form-control" id="botao" required>
                                                <option value="">Selecione</option>
                                                <option value="btn-laranja">Laranja</option>
                                                <option value="btn-padrao">Azul</option>
                                                <option value="bg-roxo">Roxo</option>
                                                <option value="bg-cinza">Cinza</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 30px">
                                        <div class="form-group col-xl-12">
                                            <input type="submit" class="form-control btn btn-primary" value="Salvar">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-xxl-6 col-lg-6">
                <div id="view_checkout" class="card card-shadow">

                    {{--  <iframe id="view_checkout" src="https://checkout.mrorganic.com.br/JTD386" style="height: 600px"></iframe>   --}}
                </div>
            </div>
        </div>
    </div>

  <script>

    $(document).ready( function(){

        var checkout = $('#view_checkout');
        {{--  $('#view_checkout').html('{!! mb_convert_encoding(file_get_contents("https://checkout.mrorganic.com.br/JTD386"), "HTML-ENTITIES", "UTF-8") !!}'.replace(/\\/g, '\\\\').replace(/"/g, '\\"').replace(/\n/g, ''));  --}}

    });

  </script>


@endsection

