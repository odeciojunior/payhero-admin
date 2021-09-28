<div style="min-height: 300px" class="card">
    <table id='transfersTable' class="table table-striped table-condensed unify">
        <thead>
        <tr>
            <td scope="col" class='headCenter' style='width:33%'>Razão</td>
            <td scope="col" class='headCenter' style='width:33%'>Data da transferência
            </td>
            <td scope="col" class='headCenter' style='width:34%'>Valor</td>
        </tr>
        </thead>
        <tbody id="table-transfers-body" class="custom-t-body" img-empty="{!! asset('modules/global/img/geral-1.svg')!!}">
        </tbody>
    </table>
</div>
<div class="row justify-content-center justify-content-md-end pr-md-15">
    <ul id="pagination-transfers" class="pagination-sm margin-chat-pagination" style="margin-top:10px;position:relative;float:right">
        {{--loaded by js...--}}
    </ul>
</div>