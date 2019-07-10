<div class='page-content container-fluid'>
    <table class='table-hover' style='width: 100%'>
        <tbody>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Event</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$sms->event}}</td>
                <br>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Tempo</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$sms->time}}</td>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Período</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$sms->period}}</td>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Mensagem</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$sms->message}}</td>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Status</th>
                <td style='width: 20px'></td>
                <td class='text-left'>
                    @if($sms->status == 1)
                        <span class='badge badge-success text-left'>Ativo</span>
                    @else
                        <span class='badge badge-danger'>Desativado</span>
                    @endif
                </td>
            </tr>

        </tbody>
    </table>
</div>
