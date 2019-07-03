<div class='page-content container-fluid'>
    <table class='table-hover' style='width: 100%'>
        <tbody>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Descrição</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$coupon->name}}</td>
                <br>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Code</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$coupon->code}}</td>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Tipo</th>
                <td style='width: 20px'></td>
                <td class='text-left'>{{$coupon->type == 1 ? 'Valor':'Porcentagem'}}</td>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center '>Valor</th>
                <td style='width: 20px'></td>
                <td class='text-left value-coupon'>{{$coupon->value}}</td>
            </tr>
            <tr style='height: 40%;'>
                <th style='width:40%;' class='text-center'>Status</th>
                <td style='width: 20px'></td>
                <td class='text-left'>
                    @if($coupon->status == 1)
                        <span class='badge badge-success text-left'>Ativo</span>
                    @else
                        <span class='badge badge-danger'>Desativado</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>
