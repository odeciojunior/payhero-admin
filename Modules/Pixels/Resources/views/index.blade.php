<div class='row'>
    <div style='width:100%'>
        <a id='add-pixel' class='btn btn-primary float-right' data-toggle='modal' data-target='#modal-content' style='color:white;'>
            <i class='icon wb-user-add' aria-hidden='true'></i>Adicionar Pixel
        </a>
    </div>
</div>
<div class='panel pt-10 p-10' style='min-height: 300px'>
    <div class='page-invoice-table table-responsive'>
        <table id='table-pixel' class='table text-right table-pixels table-hover' style='width:100%'>
            <thead style='text-align:center;'>
                <tr>
                    <th style='vertical-align: middle;' class='table-title text-center'><b>Nome</b></th>
                    <th style='vertical-align: middle;' class='table-title text-center'><b>Código</b></th>
                    <th style='vertical-align: middle;' class='table-title text-center'><b>Plataforma</b></th>
                    <th style='vertical-align: middle;' class='table-title text-center'><b>Status</b></th>
                </tr>
            </thead>
            <tbody id='data-table-pixel'>
                {{-- js carregando dados --}}
            </tbody>
        </table>
    </div>
</div>
{{--<table id="tabela_pixels" class="table-bordered table-hover w-full" style="margin-top: 80px">--}}
    {{--<a id="adicionar_pixel" class="btn btn-primary float-right" data-toggle='modal' data-target='#modal_add' style="color: white">--}}
        {{--<i class='icon wb-user-add' aria-hidden='true'></i> Adicionar pixel--}}
    {{--</a>--}}
    {{--<thead class="bg-blue-grey-100">--}}
        {{--<th>Nome</th>--}}
        {{--<th>Código</th>--}}
        {{--<th>Plataforma</th>--}}
        {{--<th>Status</th>--}}
        {{--<th style="min-width: 159px;max-width:161px;width:160px">Detalhes</th>--}}
    {{--</thead>--}}
    {{--<tbody>--}}
    {{--</tbody>--}}
{{--</table>--}}