<div class="col-12 mb-3 mt-3">
    <h5 class="card-title"> Histórico de saques </h5>
</div>
<div style="min-height: 300px" class="card">
    <table id='withdrawalsTable' class="table table-striped table-condensed unify">
        <thead>
            <tr>
                <td class="table-title" scope="col">Código</td>
                <td scope="col" class="table-title">Conta</td>
                <td scope="col" class="table-title">Solicitação</td>
                <td scope="col" class="table-title">Liberação</td>
                <td scope="col" class="table-title">Status</td>
                <td scope="col" class="table-title">Valor</td>
            </tr>
        </thead>
        <tbody id="withdrawals-table-data" class="custom-t-body" img-empty="{!! asset('modules/global/img/extrato.svg')!!}">
        </tbody>
    </table>
</div>
<div class="row justify-content-center justify-content-md-end pr-md-15">
    <ul id="pagination-withdrawals"
        class="d-inline-flex flex-wrap justify-content-center pl-10 mt-10">
        {{--js carrega...--}}
    </ul>
</div>