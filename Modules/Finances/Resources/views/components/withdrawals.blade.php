<h4 class="d-block d-md-none title-withdrawals-table">Histórico de transferências</h4>
<div class="card withdrawals-table">
    <table id='withdrawalsTable' class="table table-striped table-condensed unify">
        <thead>
            <tr>
                <td class="">Código do saque</td>
                <td class="">Conta</td>
                <td class="">Solicitação</td>
                <td class="">Liberação</td>
                <td class="text-center text-center">Status</td>
                <td class="" style="min-width: 120px">Valor</td>
                <td class=""></td>
            </tr>
        </thead>
        <tbody id="withdrawals-table-data" class="custom-t-body" img-empty="{!! mix('build/global/img/extrato.svg') !!}">
        </tbody>
    </table>
</div>
<div class="row justify-content-end pr-15">
    <ul id="pagination-withdrawals" class="d-inline-flex flex-wrap justify-content-center pl-10 mt-10 pagination-style">
        {{-- js carrega... --}}
    </ul>
</div>
