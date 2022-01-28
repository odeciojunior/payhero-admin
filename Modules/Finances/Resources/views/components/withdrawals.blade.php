<h4 class="d-block d-md-none title-withdrawals-table">Histórico de transferências</h4>
<div class="card withdrawals-table">
    <table id='withdrawalsTable' class="table table-striped table-condensed unify">
        <thead>
            <tr>
                <td class="table-title text-center">Código do saque</td>
                <td class="table-title">Conta</td>
                <td class="table-title">Solicitação</td>
                <td class="table-title">Liberação</td>
                <td class="table-title text-center text-center">Status</td>
                <td style="min-width: 120px" class="table-title">Valor</td>
            </tr>
        </thead>
        <tbody id="withdrawals-table-data" class="custom-t-body" img-empty="{!! asset('modules/global/img/extrato.svg')!!}">
        </tbody>
    </table>
</div>
<div class="row justify-content-end pr-15">
    <ul id="pagination-withdrawals"
        class="d-inline-flex flex-wrap justify-content-center pl-10 mt-10">
        {{--js carrega...--}}
    </ul>
</div>
