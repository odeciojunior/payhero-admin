<div style="min-height: 300px" class="card">
    <table id="transfersTable" class="table table-striped table-condensed unify">
        <thead>
            <tr>
                <td class='headCenter' style='width:33%'>Razão</td>
                <td class='headCenter' style='width:33%'>Data da transferência</td>
                <td class='headCenter' style='width:34%'>Valor</td>
            </tr>
        </thead>
        <tbody id="table-transfers-body" class="custom-t-body" img-empty="{!! asset('modules/global/img/geral-1.svg')!!}"></tbody>
    </table>
    <table id="statementTable" class="table table-condensed unify table-striped" style="display: none">
        <thead>
            <tr>
                <td class="headCenter table-title">Razão</td>
                <td class="headCenter table-title">Data prevista
                    <i style="font-weight: normal" class="o-question-help-1 ml-5 font-size-14" data-toggle="tooltip"
                       title="" data-original-title="A comissão será transferida somente após informar códigos de rastreio válidos">
                    </i>
                </td>
                <td class="headCenter table-title text-center">Status</td>
                <td class="headCenter table-title">Valor</td>
            </tr>
        </thead>
        <tbody id="table-statement-body" img-empty="{!! asset('modules/global/img/geral-1.svg')!!}" class="custom-t-body table-statement-body-class">
        </tbody>
    </table>
</div>

<div class="row justify-content-center justify-content-md-end pr-md-15">
    <ul id="pagination-transfers" class="pagination-sm margin-chat-pagination" style="margin-top:10px;position:relative;float:right">
        {{--loaded by js...--}}
    </ul>
    <div id="pagination-statement" class="pagination-sm margin-chat-pagination pagination-statement-class text-xs-center text-md-right" style="margin-top: 10px; position:relative;display: none">
        {{--loaded by js...--}}
    </div>
</div>
