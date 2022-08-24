<div class="card transfersTable">
    
    <table id="transfersTable" class="table table-striped table-condensed unify">
        
        <thead>
            
            <tr>
                <td class='col-auto headCenter' style="width: 300px;">Razão</td>
                <td class='col-auto headCenter'>Data da venda</td>
                <td class='col-auto headCenter'>Data da transferência</td>
                <td class='col-auto headCenter'>Valor</td>
            </tr>

        </thead>

        <tbody id="table-transfers-body" class="custom-t-body" img-empty="{!! mix('build/global/img/geral-1.svg') !!}"></tbody>

    </table>
    
    <table id="statementTable" class="table table-condensed unify table-striped" style="display: none">

        <thead>
            <tr>
                <td class="col-auto headCenter"style="width: 300px;">Razão</td>
                
                <td class="col-auto headCenter">Data da venda</td>
                
                <td class="col-auto headCenter">Data prevista
                    <i style="font-weight: normal" class="o-question-help-1 ml-5 font-size-14" data-toggle="tooltip" title="" data-original-title="A comissão será transferida somente após informar códigos de rastreio válidos"></i>
                </td>
                
                <td class="col-auto headCenter text-center">Status</td>
                
                <td class="col-auto headCenter">Valor</td>
            
            </tr>
        
        </thead>
        
        <tbody id="table-statement-body" img-empty="{!! mix('build/global/img/geral-1.svg') !!}" class="custom-t-body table-statement-body-class"></tbody>
    
    </table>

</div>

<div class="row justify-content-end pr-15">
    
    <ul id="pagination-transfers" class="pagination-sm margin-chat-pagination pagination-statement-class text-xs-center text-md-right" style="margin-top:10px;position:relative;float:right">
        {{-- loaded by js... --}}
    </ul>

    <div id="pagination-statement" class="pagination-sm margin-chat-pagination pagination-statement-class text-xs-center text-md-right" style="margin-top: 10px; position:relative;float:right;display: none">
        {{-- loaded by js... --}}
    </div>
    
</div>
