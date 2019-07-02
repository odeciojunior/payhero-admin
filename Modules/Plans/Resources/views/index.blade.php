<div class='row'>
    <div style='width:100%'>
        <a id='add-plan' class='btn btn-primary float-right' data-toggle='modal' data-target='#modal-content' style='color:white;'>
            <i class='icon wb-user-add' aria-hidden='true'></i>Adicionar Plano
        </a>
    </div>
</div>
<div class='panel pt-10 p-10' style='min-height: 300px'>
    <div class='page-invoice-table table-responsive'>
        <table id='table-pixel' class='table text-right table-pixels table-hover' style='width:100%'>
            <thead style='text-align:center;'>
                <tr>
                    <th style='vertical-align: middle;' class='table-title text-center'><b>Nome</b></th>
                    {{--                    <th style='vertical-align: middle;' class='table-title text-center'><b>Descrição</b></th>--}}
                    <th style='vertical-align: middle;' class='table-title text-center'><b>Código</b></th>
                    <th style='vertical-align: middle;' class='table-title text-center'><b>Preço</b></th>
                    <th style='vertical-align: middle;' class='table-title text-center'><b>Status</b></th>
                </tr>
            </thead>
            <tbody id='data-table-plan'>
                {{-- js carregando dados --}}
            </tbody>
        </table>
    </div>
</div>
