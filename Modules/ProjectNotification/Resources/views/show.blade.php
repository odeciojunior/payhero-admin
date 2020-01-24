<table class='table-hover' style='width: 100%'>
    <tbody>
        <tr>
            <td class="table-title">Tipo</td>
            <td style='width: 20px'></td>
            <td class='text-left projectn-type'></td>
        </tr>
        <tr>
            <td class="table-title">Evento</td>
            <td style='width: 20px'></td>
            <td class='text-left projectn-event'></td>
        </tr>
        <tr>
            <td class="table-title">Tempo</td>
            <td style='width: 20px'></td>
            <td class='text-left projectn-time'></td>
        </tr>
        <tr>
            <td class='table-title'>Status</td>
            <td style='width: 20px'></td>
            <td class='text-left projectn-status'></td>
        </tr>
        <tr>
            <td class='table-title'>Assunto</td>
            <td style='width: 20px'></td>
            <td class='text-left projectn-subject'></td>
        </tr>
        <tr>
            <td class='table-title'>Mensagem</td>
            <td style='width: 20px'></td>
            <td class='text-left projectn-message'></td>
        </tr>
    </tbody>
</table>
<style type="text/css">
    .font-padrao {  font-family: 'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; }
    .item-text-padrao { font-size: 12px; line-height: 30px; margin: 0; color: #999999; }
    .lineh-18 { line-height: 18px; }
    .font-15 { font-size: 15px; }
    .color-999 { color: #999999 }
    .color-333 { color: #333333 }
</style>
<div class="row include-templates-email">
    @include('projectnotification::billetpaid')
</div>